<?php

namespace App\Http\Controllers\Admin\Stock;

use App\Models\Bank;
use App\Models\Unit;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PrintController;

class StockController extends Controller {

    public function index(Request $request)
    {
        $sql = Stock::orderBy('date', 'DESC')->orderBy('id', 'DESC');

        if ($request->q) {
            $sql->where('challan_number', 'LIKE', $request->q.'%');
        }

        if ($request->supplier) {
            $sql->where('supplier_id', $request->supplier);
        }

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $stocks = $sql->paginate($request->limit ?? 15);
        
        $suppliers = Supplier::where('status', 'Active')->get();

        return view('admin.stock.index', compact('stocks', 'suppliers'))->with('list', 1);
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
        
        $suppliers = Supplier::where('status', 'Active')->get();
        $products = Product::where('status', 'Active')->get();
        $units = Unit::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.stock.index', compact('items', 'suppliers', 'products', 'units', 'banks'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'supplier_id' => 'required|integer',
            'date' => 'required|date',
            'challan_number' => 'nullable|max:255',
            'challan_date' => 'nullable|date',
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
            'supplier_id' => $request->supplier_id,
            'date' => dbDateFormat($request->date),
            'challan_number' => $request->challan_number,
            'challan_date' => $request->challan_date,
            'total_quantity' => $request->total_quantity,
            'subtotal_amount' => $request->subtotal_amount,
            'discount_amount' => $request->discount_amount,
            'tax_percent' => $request->tax_percent,
            'tax_amount' => $request->tax_amount,
            'total_amount' => $request->total_amount,
        ];
        $stock = Stock::create($storeData);

        if ($request->only('stock_item_id')) {
            $data = [];
            foreach ($request->stock_item_id as $key => $row) {
                $unit = Unit::find($request->unit_id[$key]);

                $data[] = [
                    'stock_id' => $stock->id,
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

            StockItem::insert($data);
        }

        if ($request->paid_amount > 0) {
            $paidData = [
                'supplier_id' => $stock->supplier_id,
                'bank_id' => $request->bank_id,
                'type' => 'Out',
                'date' => dbDateFormat($stock->date),
                'note' => null,
                'amount' => $request->paid_amount,
                'stock_id' => $stock->id,
            ];
            $payment = SupplierPayment::create($paidData);

            if ($payment) {
                Transaction::create([
                    'type' => 'Out',
                    'flag' => 'Supplier Payment',
                    'flagable_id' => $payment->id,
                    'flagable_type' => 'App\Models\SupplierPayment',
                    'bank_id' => $payment->bank_id,
                    'datetime' => now(),
                    'note' => $payment->note,
                    'amount' => $payment->amount,
                ]);
            }
        }

        $request->session()->flash('successMessage', 'Stock was successfully added!');
        return redirect()->route('stock.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $data = Stock::select('stocks.*', 'supplier_payments.bank_id', 'supplier_payments.amount AS paid_amount')->with('items')
        ->leftJoin('supplier_payments', function($q) {
            $q->on('supplier_payments.stock_id', '=', 'stocks.id');
            $q->whereNull('supplier_payments.deleted_at');
        })
        ->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('stock.index', qArray());
        }

        return view('admin.stock.index', compact('data'))->with('show', $id);
    }

    public function prints(Request $request, $id)
    {
        $data = Stock::with('items')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('stock.index', qArray());
        }

        $paidAmount = SupplierPayment::where('stock_id', $id)->sum('amount');

        $sql = Supplier::select(DB::raw('(suppliers.previous_due - (IFNULL(A.stockAmount, 0) - IFNULL(B.returnAmount, 0)) - (IFNULL(D.outAmount, 0) - IFNULL(C.inAmount, 0))) AS dueAmount'))
        ->where('suppliers.id', $data->supplier_id);
        $sql->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_amount) AS stockAmount FROM `stocks` WHERE deleted_at IS NULL GROUP BY supplier_id) AS A"), function($q) {
            $q->on('A.supplier_id', '=', 'suppliers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT X.supplier_id, SUM(Y.amount) AS returnAmount FROM `stock_returns` AS X INNER JOIN stock_return_items AS Y ON X.id = Y.stock_id WHERE X.deleted_at IS NULL GROUP BY supplier_id) AS B"), function($q) {
            $q->on('B.supplier_id', '=', 'suppliers.id');
        });

        $sql->leftJoin(DB::raw("(SELECT supplier_id, SUM(amount) AS inAmount FROM `supplier_payments` WHERE type='In' AND deleted_at IS NULL GROUP BY supplier_id) AS C"), function($q) {
            $q->on('C.supplier_id', '=', 'suppliers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT supplier_id, SUM(amount) AS outAmount FROM `supplier_payments` WHERE type='Out' AND deleted_at IS NULL GROUP BY supplier_id) AS D"), function($q) {
            $q->on('D.supplier_id', '=', 'suppliers.id');
        });
        $reports = $sql->first();

        if (env('DIRECT_PRINT') == 1) {
            (new PrintController())->sale($data);
            return redirect()->back();
        } else {
            return view('admin.stock.print.stock-print', compact('data', 'paidAmount', 'reports'));
        }
    }

    public function edit(Request $request, $id)
    {
        $data = Stock::select('stocks.*', 'supplier_payments.bank_id', 'supplier_payments.amount AS paid_amount')->with('items')
        ->leftJoin('supplier_payments', function($q) {
            $q->on('supplier_payments.stock_id', '=', 'stocks.id');
            $q->whereNull('supplier_payments.deleted_at');
        })
        ->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('stock.index', qArray());
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

        $suppliers = Supplier::where('status', 'Active')->get();
        $products = Product::where('status', 'Active')->get();
        $units = Unit::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.stock.index', compact('data', 'items', 'suppliers', 'products', 'units', 'banks'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'supplier_id' => 'required|integer',
            'date' => 'required|date',
            'challan_number' => 'nullable|max:255',
            'challan_date' => 'nullable|date',
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

        $data = Stock::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('stock.index', qArray());
        }

        $storeData = [
            'supplier_id' => $request->supplier_id,
            'date' => dbDateFormat($request->date),
            'challan_number' => $request->challan_number,
            'challan_date' => $request->challan_date,
            'total_quantity' => $request->total_quantity,
            'subtotal_amount' => $request->subtotal_amount,
            'discount_amount' => $request->discount_amount,
            'tax_percent' => $request->tax_percent,
            'tax_amount' => $request->tax_amount,
            'total_amount' => $request->total_amount,
        ];

        $data->update($storeData);

        if ($request->only('stock_item_id')) {
            StockItem::where('stock_id', $data->id)->whereNotIn('id', $request->stock_item_id)->delete();
            foreach ($request->stock_item_id as $key => $row) {
                $unit = Unit::find($request->unit_id[$key]);
                
                $updateData = [
                    'stock_id' => $data->id,
                    'product_id' => $request->product_id[$key],
                    'unit_id' => $request->unit_id[$key],
                    'unit_quantity' => $unit->quantity,
                    'quantity' => $request->quantity[$key],
                    'unit_price' => $request->unit_price[$key],
                    'amount' => $request->amount[$key],
                    'actual_quantity' => ($request->quantity[$key] * $unit->quantity),
                    'amount' => $request->amount[$key],
                ];

                if ($row > 0) {
                    StockItem::where('id', $row)->update($updateData);
                } else {
                    StockItem::create($updateData);
                }
            }
        }

        if ($request->paid_amount > 0) {
            $paidData = [
                'supplier_id' => $data->supplier_id,
                'bank_id' => $request->bank_id,
                'type' => 'Out',
                'date' => dbDateFormat($data->date),
                'note' => null,
                'amount' => $request->paid_amount,
                'stock_id' => $data->id,
            ];

            $payment = SupplierPayment::updateOrCreate(['stock_id' => $data->id], $paidData);
            if ($payment) {
                Transaction::updateOrCreate([
                    'flagable_id' => $payment->id,
                    'flagable_type' => 'App\Models\SupplierPayment',
                ], [
                    'type' => 'Out',
                    'flag' => 'Supplier Payment',
                    'bank_id' => $payment->bank_id,
                    'datetime' => now(),
                    'note' => $payment->note,
                    'amount' => $payment->amount,
                ]);
            }
        } else {
            $payment = SupplierPayment::where('stock_id', $data->id)->first();
            if (!empty($payment)) {
                Transaction::where('flagable_id', $payment->id)->where('flagable_type', 'App\Models\SupplierPayment')->delete();
                $payment->delete();
            }
        }

        $request->session()->flash('successMessage', 'Stock was successfully updated!');
        return redirect()->route('stock.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $data = Stock::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('stock.index', qArray());
        }

        $payment = SupplierPayment::where('stock_id', $data->id)->first();
        if (!empty($payment)) {
            Transaction::where('flagable_id', $payment->id)->where('flagable_type', 'App\Models\SupplierPayment')->delete();
            $payment->delete();
        }

        StockItem::where('stock_id', $id)->delete();
        $data->delete();
        
        $request->session()->flash('successMessage', 'Stock was successfully deleted!');
        return redirect()->route('stock.index', qArray());
    }
}
