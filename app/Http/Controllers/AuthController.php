<?php

namespace App\Http\Controllers;

use App\Models\{User,UserDetail};
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
               'first_name'     =>  $user->first_name ? $user->first_name : '',
               'last_name'      =>  $user->last_name ? $user->last_name : '',
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


    /*
    Method Name:    profile
    Purpose:        Get profile detail on the basis of bearer token
    Params:         []
    */ 
    public function profile() 
    {
        try {
            $user = Auth::user();
            $userData['first_name']     = $user->first_name ? $user->first_name : '';
            $userData['last_name']     = $user->last_name ? $user->last_name : '';
            $userData['email']          = $user->email ? $user->email : '';
            $userData['phone_number']   = $user->userdetail  ? ( $user->userdetail->phone_number ? $user->userdetail->phone_number : '') : '';
            $userData['role']           =  getRoleById($user->id);
            
            return $this->apiResponse('success', '200', 'User profile '.config('constants.SUCCESS.FETCH_DONE'), $userData);
        } catch ( \Exception $e ) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }
    /* End Method profile */

    /*
    Method Name:    detailUpdate
    Purpose:        Update user detail after login
    Params:         [first_name, last_name, phone_number, dob, address, city, state_id, country_id]
    */ 
    public function detailUpdate(Request $request)
    {  
        $validationRules = [
            'first_name'            => 'required|string|max:100', 
            'last_name'             => 'required|string|max:100', 
            'phone_number'          => 'required|max:10', 
            'email'                 => 'required|email:rfc,dns',
        ];
		
        if( getRoleById(authId()) == config('constants.ROLES.ADMINISTRATOR') ) {
            $validationRules['security_key'] = 'required|max:100|exists:users'; 
        }
        $validator = Validator::make($request->all(), $validationRules);
		if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->first());
        } 
        try {
            $user = User::findOrFail(Auth::id()); 
			$user->first_name       = $request->first_name;    
			$user->last_name        = $request->last_name;    
			$user->email            = $request->email;    

            UserDetail::updateOrCreate( ['user_id' => authId()], [
                'phone_number'=> $request->phone_number]  );
            $user->save();  

            return $this->apiResponse('success', '200', 'Profile details '.config('constants.SUCCESS.UPDATE_DONE'));
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage());
        } 
    }    
    /* End Method detailUpdate */

    /*
    Method Name:    register
    Purpose:        Register user with their infromation
    Params:         [first_name, last_name, phone_number, email, password]
    */ 
    public function register(Request $request)
    {  
        $validationRules = [
            'first_name'            => 'required|string|max:100', 
            'last_name'             => 'string|max:100', 
            'phone_number'          => 'required|max:10', 
            'email'                 => 'required|unique:users,email|email:rfc,dns',
            'password'              => 'required|min:6|same:confirm_password',
            'confirm_password'      => 'required|min:6',
        ];
		
        $validator = Validator::make($request->all(), $validationRules);
		if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->first());
        } 
        try {
            $user = User::create([
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name ? $request->last_name : Null,
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'phone_number'  => $request->phone_number,
                'role_id'       => 2,
            ]);

            $userData  = [
                // 'user_id'        =>  encryptData($user->id),
                // 'first_name'     =>  $user->first_name ? $user->first_name : '',
                // 'last_name'      =>  $user->last_name ? $user->last_name : '',
                // 'role'           =>  getRoleById($user->id),
                // 'phone_numnber'  =>  $user->userdetail ? $user->userdetail->phone_number : '',
                'email'          =>  $user->email,
                'bearer'         => Auth::attempt($request->only(['email', 'password']))
             ];
            return $this->apiResponse('success', '200', 'User '.config('constants.SUCCESS.ADD_DONE'),$userData);
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage());
        } 
    }    
    /* End Method register */
}
