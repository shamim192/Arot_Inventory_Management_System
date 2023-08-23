<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $data = User::find(Auth::user()->id);
        return view('admin.profile', compact('data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.Auth::user()->id.',id',
        ]);

        $formData = [
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
        ];
        $data = User::find(Auth::user()->id);
        $data->update($formData);

        $request->session()->flash('successMessage', $data->name."'s account was successfully updated!");
        return redirect()->route('profile');
    }

    public function password()
    {
        $data = User::find(Auth::user()->id);
        return view('admin.profile', compact('data'))->with('password', 1);
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'required',
            'password' => 'required|max:20|min:8|confirmed',
        ]);

        $data = User::find(Auth::user()->id);

        if (!Hash::check($request->current_password, $data->password)) {
            $request->session()->flash('errorMessage', "The specified password does not match the database password");
        } else {
            $formData = [
                'password' => Hash::make($request->password),
            ];
            $data->update($formData);

            $request->session()->flash('successMessage', $data->name."'s password was successfully updated!");
        }
        
        return redirect()->route('profile');
    }
}
