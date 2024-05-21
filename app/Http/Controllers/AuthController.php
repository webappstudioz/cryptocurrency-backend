<?php

namespace App\Http\Controllers;

use App\Models\{TokenManagement, User,UserDetail};
use App\Traits\SendResponseTrait;
use Illuminate\Support\Facades\{Validator, Hash, Auth}; 
use Illuminate\Support\Str;
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
            'email'                 => 'required|email:rfc,dns',
            'country_id'            => 'required',
            'term_condition'        => 'required|in:0,1',
            'password'              => 'required|min:6|same:confirm_password',
            'confirm_password'      => 'required|min:6',
        ];
		
        $validator = Validator::make($request->all(), $validationRules);
		if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->first());
        } 
        if(User::where('email',$request->email)->where('verified',1)->count()){
            $validator = Validator::make($request->all(), [
                'email'     => 'unique:users,email'
            ]);
            if ($validator->fails()) { 
                return $this->apiResponse('error', '422', $validator->errors()->first());
            } 
        }
        try {
            User::where('email', $request->email)->where('verified',0)->delete();

            $user = User::create([
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name ? $request->last_name : Null,
                'email'         => $request->email,
                'phone_number'  => $request->phone_number ? $request->phone_number : null,
                'country_id'    => $request->country_id ? decryptData($request->country_id) : null,
                'password'      => Hash::make($request->password),
                'phone_number'  => $request->phone_number,
                'referrel_Code' => $request->referrel_Code ? $request->referrel_Code : '',
                'term_condition' => $request->term_condition ? $request->term_condition : 0,
                'role_id'       => 2,
                'verified'      => 0,
            ]);

            $token = '';
            do {
                $token = Str::random(10);
            }while(TokenManagement::where('token',$token)->count());

            TokenManagement::updateOrCreate([
                'email'     => $request->email,
            ],[
                'token'     => $token
            ]);
            $userData = [
                'email'     => $request->email,
                'token'     => $token
            ];
            return $this->apiResponse('success', '200', 'User '.config('constants.SUCCESS.ADD_DONE'),$userData);
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage());
        } 
    }    
    /* End Method register */

    /*
    Method Name:    register
    Purpose:        Register user with their infromation
    Params:         [first_name, last_name, phone_number, email, password]
    */ 
    public function otpResend($token)
    {  
        
		if (TokenManagement::where('token',$token)->count() == 0) { 
            return $this->apiResponse('error', '422', 'Please provide a valid token');
        } 
       
        try {
            $otp = '';
            do {
                $otp =  random_int(100000, 999999);
            }while(TokenManagement::where('otp',$otp)->count());

            TokenManagement::where('token',$token)->update(['otp'=> $otp]);
            
            $template = $this->getTemplateByName('Email_Address_Verification');
                if( $template ) { 
                    //preparing data to send in mail 
                
                    $token_detail = TokenManagement::where('token',$token)->first();
                    $user = User::where('email',$token_detail->email)->first();  
                    $link               = config('constants.FRONTEND_URL'). config('constants.OTP_VERIFICATION') .$token;
                    $stringToReplace    = ['{{$name}}', '{{$token}}','{{$otp}}' ];
                    $stringReplaceWith  = [$user->first_name.' '.$user->last_name, $link ,$otp ];
                    $newval             = str_replace($stringToReplace, $stringReplaceWith, $template->template);
                    //mail logs
                    $emailData          = $this->mailData($user->email, $template->subject, $newval, 'Email_Address_Verification', $template->id, '', '', authId());

                    $this->mailSend($emailData);
                }
            return $this->apiResponse('success', '200', 'OTP is generated now check again email');
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage());
        } 
    }    
    /* End Method register */
    
}
