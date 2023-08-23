<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bank;
use App\Models\Transaction;
use App\Models\FundTransfer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FundTransferController extends Controller {

    public function index(Request $request)
    {
        $sql = FundTransfer::with('fromBank', 'toBank')->orderBy('date', 'DESC');

        if ($request->q) {
            $sql->where('note', 'LIKE', $request->q.'%');
        }

        if ($request->bank) {
            $sql->where(function($q) use($request) {
                $q->where('from_bank_id', $request->bank);
                $q->orWhere('to_bank_id', $request->bank);
            });
        }

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $fundTransfers = $sql->paginate($request->limit ?? 15);

        $banks = Bank::where('status', 'Active')->get();

        return view('admin.fund-transfer', compact('fundTransfers', 'banks'))->with('list', 1);
    }

    public function create()
    {
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.fund-transfer', compact('banks'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'from_bank_id' => 'required|integer',
            'to_bank_id' => 'required|integer|different:from_bank_id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        if (!$this->checkBalance($request)) {
            $request->session()->flash('errorMessage', 'Bank Balance amount not exist.');
            return redirect()->route('fund-transfer.create', qArray());
        }

        $storeData = [
            'from_bank_id' => $request->from_bank_id,
            'to_bank_id' => $request->to_bank_id,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
        ];
        $data = FundTransfer::create($storeData);

        if ($data) {
            Transaction::insert([
                [
                    'type' => 'In',
                    'flag' => 'Transfer',
                    'flagable_id' => $data->id,
                    'flagable_type' => 'App\Models\FundTransfer',
                    'bank_id' => $data->to_bank_id,
                    'datetime' => now(),
                    'note' => $data->note,
                    'amount' => $data->amount,
                    'created_at' => now(),
                ],
                [
                    'type' => 'Out',
                    'flag' => 'Transfer',
                    'flagable_id' => $data->id,
                    'flagable_type' => 'App\Models\FundTransfer',
                    'bank_id' => $data->from_bank_id,
                    'datetime' => now(),
                    'note' => $data->note,
                    'amount' => $data->amount,
                    'created_at' => now(),
                ]
            ]);
        }

        $request->session()->flash('successMessage', 'Fund Transfer was successfully added!');
        return redirect()->route('fund-transfer.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $data = FundTransfer::with('fromBank', 'toBank')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('fund-transfer.index', qArray());
        }

        return view('admin.fund-transfer', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $data = FundTransfer::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('fund-transfer.index', qArray());
        }

        $banks = Bank::where('status', 'Active')->get();

        return view('admin.fund-transfer', compact('data', 'banks'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'from_bank_id' => 'required|integer',
            'to_bank_id' => 'required|integer|different:from_bank_id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        $data = FundTransfer::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('fund-transfer.index', qArray());
        }

        if (!$this->checkBalance($request, $id)) {
            $request->session()->flash('errorMessage', 'Bank Balance amount not exist.');
            return redirect()->route('fund-transfer.edit', $id);
        }

        $storeData = [
            'from_bank_id' => $request->from_bank_id,
            'to_bank_id' => $request->to_bank_id,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
        ];

        $data->update($storeData);

        if ($data) {
            Transaction::where('flagable_id', $data->id)->where('flagable_type', 'App\Models\FundTransfer')->forceDelete();
            Transaction::insert([
                [
                    'type' => 'In',
                    'flag' => 'Transfer',
                    'flagable_id' => $data->id,
                    'flagable_type' => 'App\Models\FundTransfer',
                    'bank_id' => $data->to_bank_id,
                    'datetime' => now(),
                    'note' => $data->note,
                    'amount' => $data->amount,
                    'created_at' => now(),
                ],
                [
                    'type' => 'Out',
                    'flag' => 'Transfer',
                    'flagable_id' => $data->id,
                    'flagable_type' => 'App\Models\FundTransfer',
                    'bank_id' => $data->from_bank_id,
                    'datetime' => now(),
                    'note' => $data->note,
                    'amount' => $data->amount,
                    'created_at' => now(),
                ]
            ]);
        }

        $request->session()->flash('successMessage', 'Fund Transfer was successfully updated!');
        return redirect()->route('fund-transfer.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $data = FundTransfer::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('fund-transfer.index', qArray());
        }

        Transaction::where('flagable_id', $data->id)->where('flagable_type', 'App\Models\FundTransfer')->delete();
        $data->delete();
        
        $request->session()->flash('successMessage', 'Fund Transfer was successfully deleted!');
        return redirect()->route('fund-transfer.index', qArray());
    }

    private function checkBalance(Request $request, $editId = null)
    {
        $receive = Transaction::where('type', 'In')->where('bank_id', $request->from_bank_id)->sum('amount');
        $issue = Transaction::where('type', 'Out')->where('bank_id', $request->from_bank_id)->sum('amount');

        $balance = ($receive-$issue);
        if ($balance >= $request->amount) {
            return true;
        }
        return false;
    }
}
