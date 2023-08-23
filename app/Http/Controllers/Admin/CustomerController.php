<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller {

    public function index(Request $request)
    {
        $sql = Customer::orderBy('name', 'ASC');

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

        $customers = $sql->paginate($request->limit ?? 15);

        return view('admin.customer', compact('customers'))->with('list', 1);
    }

    public function create()
    {
        return view('admin.customer')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'required|max:255|unique:customers,mobile',
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
        Customer::create($storeData);

        $request->session()->flash('successMessage', 'Customer was successfully added!');
        return redirect()->route('customer.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $data = Customer::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('customer.index', qArray());
        }

        return view('admin.customer', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $data = Customer::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('customer.index', qArray());
        }

        return view('admin.customer', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'required|max:255|unique:customers,mobile,'.$id.',id',
            'address' => 'required',
            'shop_name' => 'required|max:255',
            'previous_due' => 'required|numeric',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = Customer::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('customer.index', qArray());
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

        $request->session()->flash('successMessage', 'Customer was successfully updated!');
        return redirect()->route('customer.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $data = Customer::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('customer.index', qArray());
        }

        $data->delete();
        
        $request->session()->flash('successMessage', 'Customer was successfully deleted!');
        return redirect()->route('customer.index', qArray());
    }
}
