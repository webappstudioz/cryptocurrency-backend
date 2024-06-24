<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    use SendResponseTrait;
    /*
        Method Name:    detail
        Developer:      Skillskore
        Purpose:        To update the payment detail
        Params:
    */
    public function detail( $id){
        try{
            $depositData = [];
            $deposit = Payment::find(decryptData($id));
        
            $depositData = [
                'id'                    => $id,
                'send_to'               => userNameById($deposit->send_to),
                'send_from'             => userNameById($deposit->send_from),
                'payment_type'          => $deposit->payment_type,
                'payment_id'            => $deposit->payment_id,
                'method_type'           => $deposit->method_type,
                'image_path'            => $deposit->image_path,
                'amount'                => $deposit->amount,
                'status'                => $deposit->status,
                'created_at'            => $deposit->created_at
            ];

            return $this->apiResponse('success', '200', 'Payment detail '. config('constants.SUCCESS.FETCH_DONE'), $depositData); 
           
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage(), $e->getLine(),$e);
        } 
    }
    /* End Method detail */

        /*
        Method Name:    changeStatus
        Developer:      Skillskore
        Purpose:        To update the  status of user
        Params:
    */
    public function changeStatus(Request $request){
        try{
            $validationRules = [
                'deposit_id'            => 'required', 
                'status'             => 'required|in:accepted,rejected', 
            ];
            
            $validator = Validator::make($request->all(), $validationRules);
            if ($validator->fails()) { 
                return $this->apiResponse('error', '422', $validator->errors()->first());
            } 

            Payment::where('id',decryptData($request->deposit_id))->update(['status'  => $request->status]);

            return $this->apiResponse('success', '200', 'Deposit Status '. config('constants.SUCESS.CHANGED_DONE')); 
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage(), $e->getLine(),$e);
        }  
    }
    /* End Method changeStatus */

    /*
        Method Name:    list
        Developer:      Skillskore
        Purpose:        To get the list of payment
        Params:
    */
    public function list(Request $request){
        try{

            $data = [];
            $depositlist = new Payment();

            if(getRoleById(authId()) != config('constants.ROLES.ADMIN')){
                $depositlist = $depositlist->where('user_id',authId());
            }
        
            $depositlist = $depositlist->orderBy('id','asc')->paginate(10);;

            $depositData = [];
            foreach($depositlist as $deposit){
                array_push($depositData,[
                    'id'            => encryptData($deposit->id),
                    'user_name'     => userNameById($deposit->send_from),
                    'created_at'    => $deposit->created_at ? $deposit->created_at : '', 
                    'status'        => $deposit->status,
                    'payment_id'    => $deposit->payment_id
                ]);
            }
            $data = [
                'data'          => $depositData,
                'current_page'  => $depositlist->currentPage(),
                'total_record'  => $depositlist->total(),
                'has_more_pages'=> $depositlist->hasMorePages(),
            ];

            return $this->apiResponse('success', '200', 'Payment detail '. config('constants.SUCCESS.FETCH_DONE'), $data); 
        
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage(), $e->getLine(),$e);
        } 
    }
    /* End Method list */

    /*
    Method Name:    deposit
    Developer:      Skillskore
    Purpose:        To payment the amount
    Params:
    */
    public function payment(Request $request){
        try{
            $validationRules = [
                'payment_id'     => 'required|string',
                'payment_type'   => 'required|in:deposit,withdraw,transfer',
                'method_type'    => 'required_if:payment_type,transfer|in:bank,tether,bitcoin,ethereum',
                'amount'         => 'required',
                'image'          => 'required_if:payment_type,deposit|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'send_to'        => 'required_if:payment_type,transfer|exists:users,user_name'
            ];
            
            $validator = Validator::make($request->all(), $validationRules);
            if ($validator->fails()) { 
                return $this->apiResponse('error', '422', $validator->errors()->first());
            } 

            $filename = '';
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileExtension = $file->getClientOriginalExtension(); 
                $filename = date('YmdHis') . '.' . $fileExtension; 
                $path = $file->move(public_path('images'), $filename);
            }
        
            $send_to = 1;
            if($request->filled('send_to')){
               $user =  User::where('user_name',$request->user_name)->first();
                $send_to = $user->id;
            }
            Payment::create([
                'send_from'     => authId(),
                'send_to'       => $send_to,
                'payment_id'    => $request->payment_id,
                'method_type'   => $request->method_type ? $request->method_type : null,
                'payment_type'  => $request->payment_type,
                'amount'        => $request->amount,
                'image_path'    => $filename
            ]);

            return $this->apiResponse('success', '200', 'Deposit '. config('constants.SUCCESS.ADD_DONE')); 
        
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage(), $e->getLine(),$e);
        } 
    }
    /* End Method deposit */

    /*
    Method Name:    detail
    Developer:      Skillskore
    Purpose:        To get the admin account detail
    Params:
    */
    public function admiAccountDetail(Request $request){
        try{

            $userData = [];
            $user = User::find(1);
        
            if($user){
                $userData = [
                    'crypto_id'             => $user->cryptodetail ? ($user->cryptodetail->crypto_id  ? $user->cryptodetail->crypto_id : '') : '',
                    'crypto_image'          => $user->cryptodetail ? ($user->cryptodetail->crypto_image  ? $user->cryptodetail->crypto_image : '') : '',

                    'bank_name'             => $user->bankdetail ? ($user->bankdetail->bank_name  ? $user->bankdetail->bank_name : '') : '',
                    'account_number'        => $user->bankdetail ? ($user->bankdetail->account_number  ? $user->bankdetail->account_number : '') : '',
                    'ifsc_code'             => $user->bankdetail ? ($user->bankdetail->ifsc_code  ? $user->bankdetail->ifsc_code : '') : '',
                    'account_holder_name'   => $user->bankdetail ? ($user->bankdetail->account_holder_name  ? $user->bankdetail->account_holder_name : '') : '',
                    'upi_id'                => $user->bankdetail ? ($user->bankdetail->upi_id  ? $user->bankdetail->upi_id : '') : '',
                    'account_image'         => $user->bankdetail ? ($user->bankdetail->account_image  ? $user->bankdetail->account_image : '') : '',
                ];
                
            }else{
                return $this->apiResponse('error', '422', 'Admin not found');
            }
            return $this->apiResponse('success', '200', 'Admin account detail '. config('constants.SUCCESS.FETCH_DONE'), $userData); 
        
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage(), $e->getLine(),$e);
        } 
    }
    /* End Method detail */
}
