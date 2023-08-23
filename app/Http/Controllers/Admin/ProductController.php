<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller {

    public function index(Request $request)
    {
        $sql = Product::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', $request->q.'%')
                    ->orWhere('base_unit', 'LIKE', $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $products = $sql->paginate($request->limit ?? 15);

        return view('admin.product', compact('products'))->with('list', 1);
    }

    public function create()
    {
        return view('admin.product')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'base_unit' => 'required|max:255',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'name' => $request->name,
            'base_unit' => $request->base_unit,
            'status' => $request->status,
        ];
        Product::create($storeData);

        $request->session()->flash('successMessage', 'Product was successfully added!');
        return redirect()->route('product.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $data = Product::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('product.index', qArray());
        }

        return view('admin.product', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $data = Product::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('product.index', qArray());
        }

        return view('admin.product', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'base_unit' => 'required|max:255',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = Product::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('product.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'base_unit' => $request->base_unit,
            'status' => $request->status,
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'Product was successfully updated!');
        return redirect()->route('product.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $data = Product::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('product.index', qArray());
        }

        $data->delete();
        
        $request->session()->flash('successMessage', 'Product was successfully deleted!');
        return redirect()->route('product.index', qArray());
    }
}
