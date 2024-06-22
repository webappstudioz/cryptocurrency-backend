<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    use SendResponseTrait;
    /*
    Method Name:    detail
    Developer:      Skillskore
    Purpose:        To get the admin account detail
    Params:
    */
    public function detail(Request $request){
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
