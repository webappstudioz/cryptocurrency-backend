<?php

namespace App\Http\Controllers;

use App\Models\{Country, PasswordReset, TokenManagement, User,UserDetail};
use App\Traits\SendResponseTrait;
use Illuminate\Support\Facades\{Validator, Hash, Auth}; 
use Illuminate\Support\Str;
use Carbon\Carbon;
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
                'email'     => 'required',
                'password'  => 'required|string|min:6',
            ], [
                'email.required'    => 'We need to know your email address',
                'password.required' => 'You can not left password empty.',
                'password.string'   => 'Password field must be a string.'
            ]);
		if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->all());
        } 
        try { 
            $user = User::where(function($query) use($request){
                $query->where('email', $request->email)
                ->orWhere('user_name',$request->email);
            })->first(); 
            
            //check user existance 
            if(!$user || !Hash::check($request->password, $user->password))
                return $this->apiResponse('error', '400', config('constants.ERROR.WRONG_CREDENTIAL'));
            //check that account is verified 
            if($user->status != '1' || $user->verified != '1') 
                return $this->apiResponse('error', '404', config('constants.ERROR.ACCOUNT_ISSUE'));
            $request->merge(['email' => $user->email]);
            $userData  = [
               'id'        =>  encryptData($user->id),
               'user_name'      =>  $user->user_name,
               'country_id'     =>  $user->country_id ? encryptData($user->country_id) : '',
               'country_name'   =>  $user->country ? $user->country->name : '',
               'first_name'     =>  $user->first_name ? $user->first_name : '',
               'last_name'      =>  $user->last_name ? $user->last_name : '',
               'role'           =>  getRoleById($user->id),
               'phone_number'  =>  $user->phone_number ? $user->phone_number : '',
               'country_code'   => $user->country ? '+'.$user->country->phonecode : '',
               'email'          =>  $user->email,
               'bearer'         =>  Auth::attempt($request->only(['email', 'password'])),
               'joining_date'   =>  $user->joining_date,
               'referral_code'  =>  $user->referral_code
            ];

            return $this->apiResponse('success', '200', 'Login successfully', $userData);
            
        } catch(\Exception $e) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }
    /* End Method login */

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
            'referral_code'         =>  'exists:users,referral_code',
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

            $supponser_by = $referral_code = '';
            do {
                $referral_code = strtoupper(Str::random(10));
            }while(User::where('referral_code',$referral_code)->count());
            if($request->filled('referral_code')){
                $supponser_by = User::where('referral_code',$request->referral_code)->first()->id;
            }

            $user = User::create([
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name ? $request->last_name : Null,
                'email'         => $request->email,
                'phone_number'  => $request->phone_number ? $request->phone_number : null,
                'country_id'    => $request->country_id ? decryptData($request->country_id) : null,
                'password'      => Hash::make($request->password),
                'phone_number'  => $request->phone_number,
                'term_condition' => $request->term_condition ? $request->term_condition : 0,
                'referral_code' => $referral_code,
                'role_id'       => 2,
                'supponser_by'  => isset($supponser_by) ? $supponser_by :null,
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
            return $this->apiResponse('success', '200', 'Welcome to '.config('constants.COMPANYNAME').' your account has been created successfully. please verify your OTP for first login.',$userData);
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage());
        } 
    }    
    /* End Method register */

    /*
    Method Name:    otpResend
    Purpose:        Send the otp to verify the user
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
    /* End Method otpResend */
    
    /*
    Method Name:    register
    Purpose:        Register user with their infromation
    Params:         [first_name, last_name, phone_number, email, password]
    */ 
    public function otpVerify(Request $request)
    {  
        
        $validationRules = [
            'token'           => 'required', 
            'otp'             => 'required', 
        ];
		
        $validator = Validator::make($request->all(), $validationRules);
		if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->first());
        } 
		if (TokenManagement::where('token',$request->token)->where('otp',$request->otp)->count() == 0) { 
            return $this->apiResponse('error', '422', 'Please provide a valid token and OTP');
        } 
        $token_detail = TokenManagement::where('token',$request->token)->where('otp',$request->otp)->first();

        $createdAt = Carbon::parse($token_detail->updated_at);
        $now = Carbon::now();

        if ($createdAt->diffInMinutes($now) > 60) {
            return $this->apiResponse('error', '422', 'Token expired');
        }
       
        try {
            $userName = '';
            do {
                $userName =  'C2C'.random_int(1000, 9999);
            }while(User::where('user_name',$userName)->count());

            User::where('email',$token_detail->email)->update([
                        'user_name'     => $userName,
                        'verified'      => 1,
                        'joining_date'  => date('Y-m-d')]);
            
            // $token_detail->delete();
            $template = $this->getTemplateByName('THANKS_EMAIL');
                if( $template ) { 
                    //preparing data to send in mail 
                
                    $user = User::where('email',$token_detail->email)->first();  
                    $link               = config('constants.FRONTEND_URL'). config('constants.LOGIN') ;
                    $stringToReplace    = ['{{$name}}', '{{$token}}','{{$user_name}}' ];
                    $stringReplaceWith  = [$user->first_name.' '.$user->last_name, $link ,$userName ];
                    $newval             = str_replace($stringToReplace, $stringReplaceWith, $template->template);
                    //mail logs
                    $emailData          = $this->mailData($user->email, $template->subject, $newval, 'THANKS_EMAIL', $template->id, '', '', authId());

                    $this->mailSend($emailData);
            }
            $token_detail->delete();

            return $this->apiResponse('success', '200', 'You have successfully verified your email address.');
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage());
        } 
    }    
    /* End Method register */

        /*
    Method Name:    resetPassword
    Purpose:        Send email tob forgot password
    Params:         [email]
    */ 
    public function resetPassword(Request $request)
    { 
        // return $request->all(); 
        $validator = Validator::make($request->all(), 
                        ['email'=> 'required|email:rfc,dns|exists:users,email']);
		if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->first());
        } 
       
        try {

            $user = User::where('email', $request->email)->first();

            if($user->verified == 0)
                return $this->apiResponse('error', '400', config('constants.ERROR.WRONG_CREDENTIAL'));
           
            $template = $this->getTemplateByName('Forgot_Password');
                if( $template ) { 
                    $token = '';
                    do {
                        $token = Str::random(10);
                    }while(PasswordReset::where('token',$token)->count());

                    $passwordReset = PasswordReset::updateOrCreate( ['email' => $user->email],
                        [ 'email' => $user->email, 'token' => $token ,'created_at' => date('Y-m-d H:i:s')] );
                    //preparing data to send in mail 
                    $link               = config('constants.FRONTEND_URL'). config('constants.VERIFICATION') .$token;
                    $stringToReplace    = ['{{$name}}', '{{$token}}' ];
                    $stringReplaceWith  = [$user->first_name.' '.$user->last_name, $link ];
                    $newval             = str_replace($stringToReplace, $stringReplaceWith, $template->template);
                    //mail logs
                    $emailData          = $this->mailData($user->email, $template->subject, $newval, 'Forgot_Password', $template->id, '', '', authId());

                    $this->mailSend($emailData);
                }
            return $this->apiResponse('success', '200', config('constants.SUCCESS.RESET_LINK_MAIL'));
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage());
        } 
    }    
    /* End Method resetPassword */


    /*
    Method Name:    resetPasswordVerify
    Purpose:        check the reset password token verification
    Params:         [token]
    */ 
    public function resetPasswordVerify($token)
    {  
        try {
            if (PasswordReset::where('token',$token)->count() == 0) { 
                return $this->apiResponse('error', '422', 'Please provide a valid token');
            } 
            $token_detail = PasswordReset::where('token',$token)->first();
    
            $createdAt = Carbon::parse($token_detail->updated_at);
            $now = Carbon::now();
    
            if ($createdAt->diffInMinutes($now) > 60) {
                return $this->apiResponse('error', '422', 'Token expired');
            }

            return $this->apiResponse('success', '200', 'Token Verified');
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage());
        } 
    }    
    /* End Method setNewPassword */

    /*
    Method Name:    setNewPassword
    Purpose:        Send email tob forgot password
    Params:         [email]
    */ 
    public function setNewPassword(Request $request)
    {  
        $validator = Validator::make($request->all(), 
                        [
                            'token' => 'required|exists:password_resets,token',
                            'password'=> 'required_with:password_confirmation|string|confirmed'
                        ]);
		if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->first());
        } 
        try {

            $data = [
                'password' => Hash::make($request->password),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ];
            $passwordReset = PasswordReset::where('token',$request->token)->first();
            $record = User::where('email', $passwordReset->email)->update($data);
            PasswordReset::where('email', $passwordReset->email)->delete();
               
            return $this->apiResponse('success', '200','Password ' .config('constants.SUCCESS.UPDATE_DONE'));
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage());
        } 
    }    
    /* End Method setNewPassword */
    
}
