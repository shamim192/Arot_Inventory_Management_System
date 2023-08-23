<?php

namespace App\Http\Controllers\Admin\Stock;

use App\Models\Bank;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PrintController;

class SupplierPaymentController extends Controller {

    public function index(Request $request)
    {
        $sql = SupplierPayment::with(['supplier', 'bank'])->orderBy('date', 'DESC');

        if ($request->q) {
            $sql->where('note', 'LIKE', $request->q.'%');
        }

        if ($request->supplier) {
            $sql->where('supplier_id', $request->supplier);
        }

        if ($request->bank) {
            $sql->where('bank_id', $request->bank);
        }

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $payments = $sql->paginate($request->limit ?? 15);

        $suppliers = Supplier::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.stock.supplier-payment', compact('payments', 'suppliers', 'banks'))->with('list', 1);
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.stock.supplier-payment', compact('suppliers', 'banks'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'supplier_id' => 'required|integer',
            'bank_id' => 'required|integer',
            'type' => 'required|in:In,Out',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        $storeData = [
            'supplier_id' => $request->supplier_id,
            'bank_id' => $request->bank_id,
            'type' => $request->type,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
        ];
        $data = SupplierPayment::create($storeData);

        if ($data) {
            Transaction::create([
                'type' => $request->type,
                'flag' => 'Supplier Payment',
                'flagable_id' => $data->id,
                'flagable_type' => 'App\Models\SupplierPayment',
                'bank_id' => $data->bank_id,
                'datetime' => now(),
                'note' => $data->note,
                'amount' => $data->amount,
            ]);
        }

        $request->session()->flash('successMessage', 'Payment was successfully added!');
        return redirect()->route('supplier-payment.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $data = SupplierPayment::with(['supplier', 'bank'])->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('supplier-payment.index', qArray());
        }

        return view('admin.stock.supplier-payment', compact('data'))->with('show', $id);
    }
 
    public function prints(Request $request, $id)
    {
        $data = SupplierPayment::with(['supplier', 'bank'])->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('supplier-payment.index', qArray());
        }

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
            (new PrintController())->customerPayment($data);
            return redirect()->back();
        } else {
            return view('admin.stock.print.payment-print', compact('data', 'reports'));
        }
    }

    public function edit(Request $request, $id)
    {
        $data = SupplierPayment::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('supplier-payment.index', qArray());
        }

        $suppliers = Supplier::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.stock.supplier-payment', compact('data', 'suppliers', 'banks'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'supplier_id' => 'required|integer',
            'bank_id' => 'required|integer',
            'type' => 'required|in:In,Out',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        $data = SupplierPayment::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('supplier-payment.index', qArray());
        }

        $storeData = [
            'supplier_id' => $request->supplier_id,
            'bank_id' => $request->bank_id,
            'type' => $request->type,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
        ];

        $data->update($storeData);

        if ($data) {
            Transaction::updateOrCreate([
                'flagable_id' => $data->id,
                'flagable_type' => 'App\Models\SupplierPayment',
            ], [
                'type' => $request->type,
                'flag' => 'Supplier Payment',
                'bank_id' => $data->bank_id,
                'datetime' => now(),
                'note' => $data->note,
                'amount' => $data->amount,
            ]);
        }

        $request->session()->flash('successMessage', 'Payment was successfully updated!');
        return redirect()->route('supplier-payment.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $data = SupplierPayment::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('supplier-payment.index', qArray());
        }

        Transaction::where('flagable_id', $data->id)->where('flagable_type', 'App\Models\SupplierPayment')->delete();
        $data->delete();
        
        $request->session()->flash('successMessage', 'Payment was successfully deleted!');
        return redirect()->route('supplier-payment.index', qArray());
    }
}
