<?php

namespace App\Http\Controllers\Admin\Sale;

use App\Models\Bank;
use App\Models\Sale;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\CustomerPayment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PrintController;

class SaleController extends Controller {

    public function index(Request $request)
    {
        $sql = Sale::orderBy('date', 'DESC')->orderBy('id', 'DESC');

        if ($request->customer) {
            $sql->where('customer_id', $request->customer);
        }

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $sales = $sql->paginate($request->limit ?? 15);
        
        $customers = Customer::where('status', 'Active')->get();

        return view('admin.sale.index', compact('sales', 'customers'))->with('list', 1);
    }

    public function create()
    {
        $items = [
            (object)[
                'id' => null,
                'product_id' => null,
                'unit_id' => null,
                'quantity' => null,
                'unit_price' => null,
                'amount' => null,
            ]
        ];
        
        $customers = Customer::where('status', 'Active')->get();
        $products = Product::getStock();
        $units = Unit::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.sale.index', compact('items', 'customers', 'products', 'units', 'banks'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required|integer',
            'date' => 'required|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer',
            'unit_id' => 'required|array|min:1',
            'unit_id.*' => 'required|integer',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric',
        ]);

        if ($request->paid_amount > 0) {
            $this->validate($request, [
                'bank_id' => 'required|integer',
            ]);
        }

        $storeData = [
            'customer_id' => $request->customer_id,
            'date' => dbDateFormat($request->date),
            'total_quantity' => $request->total_quantity,
            'subtotal_amount' => $request->subtotal_amount,
            'discount_amount' => $request->discount_amount,
            'vat_percent' => env('VAT_PERCENT'),
            'vat_amount' => $request->vat_amount,
            'total_amount' => $request->total_amount,
        ];
        $data = Sale::create($storeData);

        if ($request->only('sale_item_id')) {
            $itemData = [];
            foreach ($request->sale_item_id as $key => $row) {
                $unit = Unit::find($request->unit_id[$key]);

                $itemData[] = [
                    'sale_id' => $data->id,
                    'product_id' => $request->product_id[$key],
                    'unit_id' => $request->unit_id[$key],
                    'unit_quantity' => $unit->quantity,
                    'quantity' => $request->quantity[$key],
                    'unit_price' => $request->unit_price[$key],
                    'amount' => $request->amount[$key],
                    'actual_quantity' => ($request->quantity[$key] * $unit->quantity),
                    'created_at' => now(),
                ];
            }

            SaleItem::insert($itemData);
        }

        if ($request->paid_amount > 0) {
            $paidData = [
                'customer_id' => $data->customer_id,
                'bank_id' => $request->bank_id,
                'type' => 'In',
                'date' => dbDateFormat($data->date),
                'note' => null,
                'amount' => $request->paid_amount,
                'sale_id' => $data->id,
            ];
            $payment = CustomerPayment::create($paidData);

            if ($payment) {
                Transaction::create([
                    'type' => 'In',
                    'flag' => 'Customer Payment',
                    'flagable_id' => $payment->id,
                    'flagable_type' => 'App\Models\CustomerPayment',
                    'bank_id' => $payment->bank_id,
                    'datetime' => now(),
                    'note' => $payment->note,
                    'amount' => $payment->amount,
                ]);
            }
        }

        if (env('DIRECT_PRINT') == 1) {
            (new PrintController())->sale($data);

            $request->session()->flash('successMessage', 'Sale was successfully added!');
            return redirect()->route('sale.create');
        } else {
            return redirect()->route('sale.print', $data->id);
        }
    }

    public function show(Request $request, $id)
    {
        $data = Sale::select('sales.*', 'customer_payments.bank_id', 'customer_payments.amount AS paid_amount')->with('items')
        ->leftJoin('customer_payments', function($q) {
            $q->on('customer_payments.sale_id', '=', 'sales.id');
            $q->whereNull('customer_payments.deleted_at');
        })
        ->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('sale.index', qArray());
        }

        return view('admin.sale.index', compact('data'))->with('show', $id);
    }

    public function prints(Request $request, $id)
    {
        $data = Sale::with('items')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('sale.index', qArray());
        }

        $paidAmount = CustomerPayment::where('sale_id', $id)->sum('amount');

        $sql = Customer::select(DB::raw('(customers.previous_due + (IFNULL(A.saleAmount, 0) - IFNULL(B.returnAmount, 0)) - (IFNULL(C.inAmount, 0) - IFNULL(D.outAmount, 0))) AS dueAmount'))
        ->where('customers.id', $data->customer_id);
        $sql->leftJoin(DB::raw("(SELECT customer_id, SUM(total_amount) AS saleAmount FROM `sales` WHERE deleted_at IS NULL GROUP BY customer_id) AS A"), function($q) {
            $q->on('A.customer_id', '=', 'customers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT X.customer_id, SUM(Y.amount) AS returnAmount FROM `sale_returns` AS X INNER JOIN sale_return_items AS Y ON X.id = Y.sale_id WHERE X.deleted_at IS NULL GROUP BY customer_id) AS B"), function($q) {
            $q->on('B.customer_id', '=', 'customers.id');
        });

        $sql->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS inAmount FROM `customer_payments` WHERE type='In' AND deleted_at IS NULL GROUP BY customer_id) AS C"), function($q) {
            $q->on('C.customer_id', '=', 'customers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS outAmount FROM `customer_payments` WHERE type='Out' AND deleted_at IS NULL GROUP BY customer_id) AS D"), function($q) {
            $q->on('D.customer_id', '=', 'customers.id');
        });
        $reports = $sql->first();

        if (env('DIRECT_PRINT') == 1) {
            (new PrintController())->sale($data);
            return redirect()->back();
        } else {
            return view('admin.sale.print.sale-print', compact('data', 'paidAmount', 'reports'));
        }
    }

    public function edit(Request $request, $id)
    {
        $data = Sale::select('sales.*', 'customer_payments.bank_id', 'customer_payments.amount AS paid_amount')->with('items')
        ->leftJoin('customer_payments', function($q) {
            $q->on('customer_payments.sale_id', '=', 'sales.id');
            $q->whereNull('customer_payments.deleted_at');
        })
        ->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('sale.index', qArray());
        }
        if ($data->items != null) {
            $items = $data->items;
        } else {
            $items = [
                (object)[
                    'id' => null,
                    'product_id' => null,
                    'unit_id' => null,
                    'quantity' => null,
                    'unit_price' => null,
                    'amount' => null,
                ]
            ];
        }

        $customers = Customer::where('status', 'Active')->get();
        $products = Product::getStock($id);
        $units = Unit::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.sale.index', compact('data', 'items', 'customers', 'products', 'units', 'banks'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'customer_id' => 'required|integer',
            'date' => 'required|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer',
            'unit_id' => 'required|array|min:1',
            'unit_id.*' => 'required|integer',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric',
        ]);
        
        if ($request->paid_amount > 0) {
            $this->validate($request, [
                'bank_id' => 'required|integer',
            ]);
        }

        $data = Sale::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('sale.index', qArray());
        }

        $storeData = [
            'customer_id' => $request->customer_id,
            'date' => dbDateFormat($request->date),
            'total_quantity' => $request->total_quantity,
            'subtotal_amount' => $request->subtotal_amount,
            'discount_amount' => $request->discount_amount,
            'vat_percent' => env('VAT_PERCENT'),
            'vat_amount' => $request->vat_amount,
            'total_amount' => $request->total_amount,
        ];

        $data->update($storeData);

        if ($request->only('sale_item_id')) {
            SaleItem::where('sale_id', $data->id)->whereNotIn('id', $request->sale_item_id)->delete();
            foreach ($request->sale_item_id as $key => $row) {
                $unit = Unit::find($request->unit_id[$key]);

                $updateData = [
                    'sale_id' => $data->id,
                    'product_id' => $request->product_id[$key],
                    'unit_id' => $request->unit_id[$key],
                    'unit_quantity' => $unit->quantity,
                    'quantity' => $request->quantity[$key],
                    'unit_price' => $request->unit_price[$key],
                    'amount' => $request->amount[$key],
                    'actual_quantity' => ($request->quantity[$key] * $unit->quantity),
                ];

                if ($row > 0) {
                    SaleItem::where('id', $row)->update($updateData);
                } else {
                    SaleItem::create($updateData);
                }
            }
        }

        if ($request->paid_amount > 0) {
            $paidData = [
                'customer_id' => $data->customer_id,
                'bank_id' => $request->bank_id,
                'type' => 'In',
                'date' => dbDateFormat($data->date),
                'note' => null,
                'amount' => $request->paid_amount,
            ];
            $payment = CustomerPayment::updateOrCreate(['sale_id' => $data->id], $paidData);
            if ($payment) {
                Transaction::updateOrCreate([
                    'flagable_id' => $payment->id,
                    'flagable_type' => 'App\Models\CustomerPayment',
                ], [
                    'type' => 'In',
                    'flag' => 'Customer Payment',
                    'bank_id' => $payment->bank_id,
                    'datetime' => now(),
                    'note' => $payment->note,
                    'amount' => $payment->amount,
                ]);
            }
        } else {
            $payment = CustomerPayment::where('sale_id', $data->id)->first();
            if (!empty($payment)) {
                Transaction::where('flagable_id', $payment->id)->where('flagable_type', 'App\Models\CustomerPayment')->delete();
                $payment->delete();
            }
        }

        (new PrintController())->sale($data);
        

        if (env('DIRECT_PRINT') == 1) {
            (new PrintController())->sale($data);

            $request->session()->flash('successMessage', 'Sale was successfully updated!');
            return redirect()->route('sale.index', qArray());
        } else {
            return redirect()->route('sale.print', $data->id);
        }
    }

    public function destroy(Request $request, $id)
    {
        $data = Sale::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('sale.index', qArray());
        }

        $payment = CustomerPayment::where('sale_id', $id)->first();
        if (!empty($payment)) {
            Transaction::where('flagable_id', $payment->id)->where('flagable_type', 'App\Models\CustomerPayment')->delete();
            $payment->delete();
        }

        SaleItem::where('sale_id', $id)->delete();
        $data->delete();
        
        $request->session()->flash('successMessage', 'Sale was successfully deleted!');
        return redirect()->route('sale.index', qArray());
    }
}
