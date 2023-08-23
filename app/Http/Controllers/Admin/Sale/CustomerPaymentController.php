<?php

namespace App\Http\Controllers\Admin\Sale;

use App\Models\Bank;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\CustomerPayment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PrintController;

class CustomerPaymentController extends Controller {

    public function index(Request $request)
    {
        $sql = CustomerPayment::with(['customer', 'bank'])->orderBy('date', 'DESC');

        if ($request->q) {
            $sql->where('note', 'LIKE', $request->q.'%');
        }

        if ($request->customer) {
            $sql->where('customer_id', $request->customer);
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

        $customers = Customer::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.sale.customer-payment', compact('payments', 'customers', 'banks'))->with('list', 1);
    }

    public function create()
    {
        $customers = Customer::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.sale.customer-payment', compact('customers', 'banks'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required|integer',
            'bank_id' => 'required|integer',
            'type' => 'required|in:In,Out',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        $storeData = [
            'customer_id' => $request->customer_id,
            'bank_id' => $request->bank_id,
            'type' => $request->type,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
        ];
        $data = CustomerPayment::create($storeData);

        if ($data) {
            Transaction::create([
                'type' => $request->type,
                'flag' => 'Customer Payment',
                'flagable_id' => $data->id,
                'flagable_type' => 'App\Models\CustomerPayment',
                'bank_id' => $data->bank_id,
                'datetime' => now(),
                'note' => $data->note,
                'amount' => $data->amount,
            ]);
        }

        if (env('DIRECT_PRINT') == 1) {
            (new PrintController())->customerPayment($data);

            $request->session()->flash('successMessage', 'Payment was successfully added!');
            return redirect()->route('customer-payment.create', qArray());
        } else {
            return redirect()->route('customer-payment.print', $data->id);
        }
    }

    public function show(Request $request, $id)
    {
        $data = CustomerPayment::with(['customer', 'bank'])->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('customer-payment.index', qArray());
        }

        return view('admin.sale.customer-payment', compact('data'))->with('show', $id);
    }
 
    public function prints(Request $request, $id)
    {
        $data = CustomerPayment::with(['customer', 'bank'])->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('customer-payment.index', qArray());
        }

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
            (new PrintController())->customerPayment($data);
            return redirect()->back();
        } else {
            return view('admin.sale.print.payment-print', compact('data', 'reports'));
        }
    }

    public function edit(Request $request, $id)
    {
        $data = CustomerPayment::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('customer-payment.index', qArray());
        }

        $customers = Customer::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.sale.customer-payment', compact('data', 'customers', 'banks'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'customer_id' => 'required|integer',
            'bank_id' => 'required|integer',
            'type' => 'required|in:In,Out',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        $data = CustomerPayment::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('customer-payment.index', qArray());
        }

        $storeData = [
            'customer_id' => $request->customer_id,
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
                'flagable_type' => 'App\Models\CustomerPayment',
            ], [
                'type' => $request->type,
                'flag' => 'Customer Payment',
                'bank_id' => $data->bank_id,
                'datetime' => now(),
                'note' => $data->note,
                'amount' => $data->amount,
            ]);
        }

        if (env('DIRECT_PRINT') == 1) {
            (new PrintController())->customerPayment($data);

            $request->session()->flash('successMessage', 'Payment was successfully updated!');
            return redirect()->route('customer-payment.index', qArray());
        } else {
            return redirect()->route('customer-payment.print', $data->id);
        }
    }

    public function destroy(Request $request, $id)
    {
        $data = CustomerPayment::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('customer-payment.index', qArray());
        }

        Transaction::where('flagable_id', $data->id)->where('flagable_type', 'App\Models\CustomerPayment')->delete();
        $data->delete();
        
        $request->session()->flash('successMessage', 'Payment was successfully deleted!');
        return redirect()->route('customer-payment.index', qArray());
    }
}
