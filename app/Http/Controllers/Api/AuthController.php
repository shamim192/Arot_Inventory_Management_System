<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @bodyParam mobile string required The mobile number of the user. Example: 017111111111
     * @bodyParam password string required The password of the user. Example: 12345678
     * @response {
        * "status": true,
        * "message": "Login Successful.",
        * "token": "API Token",
        * "data": {
            * "id": 1,
            * "name": "Sudip Palash",
            * "mobile": "01711111111",
            * "email": "user@gmail.com",
            * "account_type": "Admin",
            * "status": "Pending"
        * }
     * }
     */
    public function login(Request $request)
    {
        $credentials = $request->only('mobile', 'password');
        $validator = Validator::make($credentials, [
            'mobile' => 'required|max:191',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message'=> implode(", " , $validator->messages()->all()), 'code' => 401, 'error_type' => 'Validation Error'], 401);
        }

        $user = User::where('mobile', $request->mobile)->first();
        if (empty($user)) {
            return response()->json(['status' => false, 'message' => 'Unauthorized', 'code' => 401, 'error_type' => 'Validation Error'], 401);
        }

        if ($user->status == 'Deactivated') {
            return response()->json(['status' => false, 'message' => 'Your account is deactivated!', 'code' => 401, 'error_type' => 'Validation Error'], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['status' => false, 'message' => 'Password does not match!', 'code' => 401, 'error_type' => 'Validation Error'], 401);
        }

        $token = $user->createToken('user')->plainTextToken;

        return response()->json([
            'status'=> true,
            'message'=> 'Login Successful.',
            'token' => $token,
            'data' => $user,
        ], 200);
    }

    /**
     * @authenticated
     * @response {
        * "success": true,
        * "message": "Successfully logged out"
     * }
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json(['success'=> true, 'message' => 'Successfully logged out'], 200);
    }


    /**
     * @authenticated
     * @response {
        * "success": true,
        * "data": {
            * "id": 1,
            * "name": "Sudip Palash",
            * "mobile": "01711111111",
            * "email": "user@gmail.com",
            * "account_type": "Admin",
            * "status": "Pending"
        * }
     * }
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ], 200);
    }
}
