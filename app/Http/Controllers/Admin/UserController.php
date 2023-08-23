<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {

    public function index(Request $request)
    {
        $sql = User::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', $request->q.'%')
                ->orWhere('mobile', 'LIKE', $request->q.'%')
                ->orWhere('email', 'LIKE', $request->q.'%');
            });
        }

        if ($request->account_type) {
            $sql->where('account_type', $request->account_type);
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $users = $sql->paginate($request->limit ?? 15);

        return view('admin.user', compact('users'))->with('list', 1);
    }

    public function create()
    {
        $user = User::where('account_type', 'Admin')->get();
        return view('admin.user', compact('user'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'required|unique:users,mobile',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|max:20|min:8|confirmed',
            'account_type' => 'required|in:Admin,Staff,Warehouse Manager',
            'status' => 'required|in:Active,Deactivated',
        ]);

        

        $storeData = [
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'account_type' => $request->account_type,
            'status' => $request->status,
        ];
        User::create($storeData);

        $request->session()->flash('successMessage', 'User was successfully added!');
        return redirect()->route('user.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $data = User::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('user.index', qArray());
        }

        return view('admin.user', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $data = User::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('user.index', qArray());
        }
       

        return view('admin.user', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'required|unique:users,mobile,'.$id.',id',
            'email' => 'required|email|max:255|unique:users,email,'.$id.',id',
            'account_type' => 'required|in:Admin,Staff,Warehouse Manager',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = User::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('user.index', qArray());
        }

        

        $storeData = [
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'account_type' => $request->account_type,
            'status' => $request->status,
        ];

        if ($request->password != '') {
            $this->validate($request, [
                'password' => 'required|max:20|min:8|confirmed',
            ]);
            $storeData['password'] = Hash::make($request->password);
        }

        $data->update($storeData);

        $request->session()->flash('successMessage', 'User was successfully updated!');
        return redirect()->route('user.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $data = User::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('user.index', qArray());
        }

        $data->delete();
        
        $request->session()->flash('successMessage', 'User was successfully deleted!');
        return redirect()->route('user.index', qArray());
    }
}
