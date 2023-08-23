<?php

namespace App\Http\Controllers\Admin;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SupplierController extends Controller {

    public function index(Request $request)
    {
        $sql = Supplier::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', $request->q.'%')
                ->orWhere('mobile', 'LIKE', $request->q.'%')
                ->orWhere('shop_name', 'LIKE', $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $suppliers = $sql->paginate($request->limit ?? 15);

        return view('admin.supplier', compact('suppliers'))->with('list', 1);
    }

    public function create()
    {
        return view('admin.supplier')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'required|max:255|unique:suppliers,mobile',
            'address' => 'required',
            'shop_name' => 'required|max:255',
            'previous_due' => 'required|numeric',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'shop_name' => $request->shop_name,
            'previous_due' => $request->previous_due,
            'status' => $request->status,
        ];
        Supplier::create($storeData);

        $request->session()->flash('successMessage', 'Supplier was successfully added!');
        return redirect()->route('supplier.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $data = Supplier::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('supplier.index', qArray());
        }

        return view('admin.supplier', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $data = Supplier::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('supplier.index', qArray());
        }

        return view('admin.supplier', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'required|max:255|unique:suppliers,mobile,'.$id.',id',
            'address' => 'required',
            'shop_name' => 'required|max:255',
            'previous_due' => 'required|numeric',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = Supplier::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('supplier.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'shop_name' => $request->shop_name,
            'previous_due' => $request->previous_due,
            'status' => $request->status,
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'Supplier was successfully updated!');
        return redirect()->route('supplier.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $data = Supplier::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('supplier.index', qArray());
        }

        $data->delete();
        
        $request->session()->flash('successMessage', 'Supplier was successfully deleted!');
        return redirect()->route('supplier.index', qArray());
    }
}
