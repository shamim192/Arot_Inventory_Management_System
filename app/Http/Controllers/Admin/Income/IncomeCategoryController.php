<?php

namespace App\Http\Controllers\Admin\Income;

use App\Http\Controllers\Controller;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;

class IncomeCategoryController extends Controller
{
    public function index(Request $request)
    {
        $sql = IncomeCategory::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where('name', 'LIKE', $request->q.'%');
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $categories = $sql->paginate($request->limit ?? 15);

        return view('admin.income.category', compact('categories'))->with('list', 1);
    }

    public function create()
    {
        return view('admin.income.category')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255|unique:banks,name',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
        ];
        IncomeCategory::create($storeData);

        $request->session()->flash('successMessage', 'Category was successfully added!');
        return redirect()->route('income-category.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $data = IncomeCategory::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('income-category.index', qArray());
        }

        return view('admin.income.category', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $data = IncomeCategory::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('income-category.index', qArray());
        }

        return view('admin.income.category', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255|unique:banks,name,'.$id.',id',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = IncomeCategory::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('income-category.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'Category was successfully updated!');
        return redirect()->route('income-category.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $data = IncomeCategory::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('income-category.index', qArray());
        }

        $data->delete();
        
        $request->session()->flash('successMessage', 'Category was successfully deleted!');
        return redirect()->route('income-category.index', qArray());
    }
}
