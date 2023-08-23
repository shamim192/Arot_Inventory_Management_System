<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BankController extends Controller {

    public function index(Request $request)
    {
        $sql = Bank::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', $request->q.'%')
                ->orWhere('branch', 'LIKE', $request->q.'%')
                ->orWhere('account_number', 'LIKE', $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $banks = $sql->paginate($request->limit ?? 15);

        return view('admin.bank', compact('banks'))->with('list', 1);
    }

    public function create()
    {
        return view('admin.bank')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255|unique:banks,name',
            'branch' => 'required|max:255',
            'account_number' => 'required|max:255',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'name' => $request->name,
            'branch' => $request->branch,
            'account_number' => $request->account_number,
            'status' => $request->status,
        ];
        Bank::create($storeData);

        $request->session()->flash('successMessage', 'Bank was successfully added!');
        return redirect()->route('bank.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $data = Bank::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('bank.index', qArray());
        }

        return view('admin.bank', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $data = Bank::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('bank.index', qArray());
        }

        return view('admin.bank', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255|unique:banks,name,'.$id.',id',
            'branch' => 'required|max:255',
            'account_number' => 'required|max:255',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = Bank::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('bank.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'branch' => $request->branch,
            'account_number' => $request->account_number,
            'status' => $request->status,
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'Bank was successfully updated!');
        return redirect()->route('bank.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $data = Bank::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('bank.index', qArray());
        }

        $data->delete();
        
        $request->session()->flash('successMessage', 'Bank was successfully deleted!');
        return redirect()->route('bank.index', qArray());
    }
}
