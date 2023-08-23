<?php

namespace App\Http\Controllers\Admin;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UnitController extends Controller {

    public function index(Request $request)
    {
        $sql = Unit::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('base_unit', 'LIKE', $request->q.'%')
                ->orWhere('name', 'LIKE', $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $units = $sql->paginate($request->limit ?? 15);

        return view('admin.unit', compact('units'))->with('list', 1);
    }

    public function create()
    {
        return view('admin.unit')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'base_unit' => 'required|max:255',
            'name' => 'required|max:255',
            'quantity' => 'required|numeric',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'base_unit' => $request->base_unit,
            'name' => $request->name,
            'quantity' => $request->quantity,
            'status' => $request->status,
        ];
        Unit::create($storeData);

        $request->session()->flash('successMessage', 'Unit was successfully added!');
        return redirect()->route('unit.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $data = Unit::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('unit.index', qArray());
        }

        return view('admin.unit', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $data = Unit::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('unit.index', qArray());
        }

        return view('admin.unit', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'base_unit' => 'required|max:255',
            'name' => 'required|max:255',
            'quantity' => 'required|numeric',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = Unit::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('unit.index', qArray());
        }

        $storeData = [
            'base_unit' => $request->base_unit,
            'name' => $request->name,
            'quantity' => $request->quantity,
            'status' => $request->status,
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'Unit was successfully updated!');
        return redirect()->route('unit.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $data = Unit::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('unit.index', qArray());
        }

        $data->delete();
        
        $request->session()->flash('successMessage', 'Unit was successfully deleted!');
        return redirect()->route('unit.index', qArray());
    }
}
