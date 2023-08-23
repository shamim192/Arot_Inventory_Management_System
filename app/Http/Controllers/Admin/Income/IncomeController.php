<?php

namespace App\Http\Controllers\Admin\Income;

use App\Models\Bank;
use App\Models\Income;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\IncomeCategory;
use App\Http\Controllers\Controller;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $sql = Income::with('bank', 'category')->orderBy('date', 'DESC');

        if ($request->q) {
            $sql->where('income_number', 'LIKE', $request->q.'%')
                ->orWhere('note', 'LIKE', $request->q.'%');
        }

        if ($request->bank) {
            $sql->where('bank_id', $request->bank);
        }

        if ($request->category) {
            $sql->where('category_id', $request->category);
        }

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $incomes = $sql->paginate($request->limit ?? 15);
        
        $banks = Bank::where('status', 'Active')->get();
        $categories = IncomeCategory::where('status', 'Active')->get();

        return view('admin.income.index', compact('incomes', 'banks', 'categories'))->with('list', 1);
    }

    public function create()
    {
        $banks = Bank::where('status', 'Active')->get();
        $categories = IncomeCategory::where('status', 'Active')->get();

        return view('admin.income.index', compact('banks', 'categories'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'bank_id' => 'required|integer',
            'category_id' => 'required|integer',
            'date' => 'required|date',
            'note' => 'nullable|string',
            'amount' => 'required|numeric',
        ]);

        $chars = "0123456789";
        $code = "";
        for ($i = 0; $i < 9; $i++) {
            $code .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        $storeData = [
            'bank_id' => $request->bank_id,
            'category_id' => $request->category_id,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'income_number' => $code,
            'amount' => $request->amount,
        ];
        $data = Income::create($storeData);

        if ($data) {
            Transaction::create([
                'type' => 'In',
                'flag' => 'Income',
                'flagable_id' => $data->id,
                'flagable_type' => 'App\Models\Income',
                'bank_id' => $data->bank_id,
                'datetime' => now(),
                'note' => $data->note,
                'amount' => $data->amount,
            ]);
        }

        $request->session()->flash('successMessage', 'Income was successfully added!');
        return redirect()->route('income.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $data = Income::with('bank', 'category')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('income.index', qArray());
        }

        return view('admin.income.index', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $data = Income::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('Income.index', qArray());
        }

        $banks = Bank::where('status', 'Active')->get();
        $categories = IncomeCategory::where('status', 'Active')->get();

        return view('admin.income.index', compact('data', 'banks', 'categories'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'bank_id' => 'required|integer',
            'category_id' => 'required|integer',
            'date' => 'required|date',
            'note' => 'nullable|string',
            'amount' => 'required|numeric',
        ]);

        $data = Income::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('income.index', qArray());
        }

        $storeData = [
            'bank_id' => $request->bank_id,
            'category_id' => $request->category_id,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
        ];

        $data->update($storeData);

        if ($data) {
            Transaction::updateOrCreate([
                'flagable_id' => $data->id,
                'flagable_type' => 'App\Models\Income',
            ], [
                'type' => 'In',
                'flag' => 'Income',
                'bank_id' => $data->bank_id,
                'datetime' => now(),
                'note' => $request->note,
                'amount' => $data->amount,
            ]);
        }

        $request->session()->flash('successMessage', 'Income was successfully updated!');
        return redirect()->route('income.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $data = Income::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('income.index', qArray());
        }

        Transaction::where('flagable_id', $data->id)->where('flagable_type', 'App\Models\Income')->delete();
        $data->delete();
        
        $request->session()->flash('successMessage', 'Income was successfully deleted!');
        return redirect()->route('income.index', qArray());
    }
}
