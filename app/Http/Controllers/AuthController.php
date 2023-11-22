<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Traits\SendResponseTrait;
use Illuminate\Support\Facades\{Validator, Hash, Auth}; 

use Illuminate\Http\Request;

class AuthController extends Controller
{
    use SendResponseTrait;
    /*
    Method Name:    login
    Purpose:        To login based on email and password
    Params:         [email, password]
    */
    public function login(Request $request)
    {
        //validate incoming request
		$validator = Validator::make($request->all(), [
                'email'     => 'required|email',
                'password'  => 'required|string|min:6',
            ], [
                'email.required'    => 'We need to know your email address',
                'email.email'       => 'Provide a an valid email address',
                'password.required' => 'You can not left password empty.',
                'password.string'   => 'Password field must be a string.'
            ]);
		if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->all());
        } 
        try { 
            $user = User::where('email', $request->email)->first(); 
            
            //check user existance 
            if(!$user || !Hash::check($request->password, $user->password))
                return $this->apiResponse('error', '400', config('constants.ERROR.WRONG_CREDENTIAL'));
            //check that account is verified 
            if($user->status != '1') 
                return $this->apiResponse('error', '404', config('constants.ERROR.ACCOUNT_ISSUE'));

            $userData  = [
               'user_id'        =>  encryptData($user->id),
               'name'           =>  $user->name,
               'role'           =>  getRoleById($user->id),
               'phone_numnber'  =>  $user->userdetail ? $user->userdetail->phone_number : '',
               'email'          =>  $user->email,
               'bearer'         => Auth::attempt($request->only(['email', 'password']))
            ];

            return $this->apiResponse('success', '200', 'Login successfully', $userData);
            
        } catch(\Exception $e) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }
    /* End Method login */

}
