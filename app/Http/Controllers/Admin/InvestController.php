<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Transaction;

class InvestController extends Controller {

    public function index(Request $request)
    {
      
        $sql = Invest::with('bank')->orderBy('date', 'DESC');

        if ($request->q) {
            $sql->where('note', 'LIKE', $request->q.'%');
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

        $invests = $sql->paginate($request->limit ?? 15);

        $banks = Bank::where('status', 'Active')->get();

        return view('admin.invest', compact('invests', 'banks'))->with('list', 1);
    }

    public function create()
    {
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.invest', compact('banks'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'bank_id' => 'required|integer',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        $storeData = [
            'bank_id' => $request->bank_id,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
        ];
        $data = Invest::create($storeData);

        if ($data) {
            Transaction::create([
                'type' => 'In',
                'flag' => 'Invest',
                'flagable_id' => $data->id,
                'flagable_type' => 'App\Models\Invest',
                'bank_id' => $data->bank_id,
                'datetime' => now(),
                'note' => $data->note,
                'amount' => $data->amount,
            ]);
        }

        $request->session()->flash('successMessage', 'Invest was successfully added!');
        return redirect()->route('invest.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $data = Invest::with('bank')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('invest.index', qArray());
        }

        return view('admin.invest', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $data = Invest::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('invest.index', qArray());
        }

        $banks = Bank::where('status', 'Active')->get();

        return view('admin.invest', compact('data', 'banks'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'bank_id' => 'required|integer',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        $data = Invest::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('invest.index', qArray());
        }

        $storeData = [
            'bank_id' => $request->bank_id,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
        ];

        $data->update($storeData);

        if ($data) {
            Transaction::updateOrCreate([
                'flagable_id' => $data->id,
                'flagable_type' => 'App\Models\Invest',
            ], [
                'type' => 'In',
                'flag' => 'Invest',
                'bank_id' => $data->bank_id,
                'datetime' => now(),
                'note' => $request->note,
                'amount' => $data->amount,
            ]);
        }

        $request->session()->flash('successMessage', 'Invest was successfully updated!');
        return redirect()->route('invest.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $data = Invest::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('invest.index', qArray());
        }

        Transaction::where('flagable_id', $data->id)->where('flagable_type', 'App\Models\Invest')->delete();
        $data->delete();
        
        $request->session()->flash('successMessage', 'Invest was successfully deleted!');
        return redirect()->route('invest.index', qArray());
    }
}
