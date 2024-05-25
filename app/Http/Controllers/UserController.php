<?php

namespace App\Http\Controllers;

use App\Models\BankAccountDetail;
use App\Models\CryptoAccountDetail;
use App\Models\User;
use App\Models\UserDetail;
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use SendResponseTrait;
   /*
    Method Name:    getList
    Developer:      Skillskore
    Purpose:        To get list of all users
    Params:
    */
    public function getList(Request $request){
        try{
            if(getRoleById(authId()) != config('constants.ROLES.ADMINISTRATOR')){
                return $this->apiResponse('error', '401', config('constants.ERROR.NO_AUTHORIZATION'));
            }

            $start = $end = $daterange = '';
            if(request('daterange_filter') && request('daterange_filter') != '') {
                $daterange = request('daterange_filter');
                $daterang = explode(' / ',$daterange);
                $start = $daterang[0];
                $end = $daterang[1];
            }

            $data = User::where('role_id',2)->when(!empty($start) && !empty($end) ,function($query) use($start ,$end) {
                        $query->whereBetween('joining_date', [$start, $end]);
                    })->when(!empty($request->search_keyword),function($qu) use($request) {
                        $qu->where('first_name', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhere('last_name', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", '%'.$$request->search_keyword.'%')
                        ->orWhere('email', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhere('phone_number', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhere('user_name', 'like', '%'.$$request->search_keyword.'%');
                    })->where('verified',1)
                    ->when(!empty($request->status),function($qu) use($request){
                        
                    });

            $data = $data->orderBy('id','asc')->paginate(10);
            $userData = [];
            foreach($data as $user){
                array_push($userData,[
                    'id'            => encryptData($user->id),
                    'user_name'     => $user->user_name,
                    'first_name'    => $user->first_name,
                    'last_name'     => $user->last_name,
                    'email'         => $user->email,
                    'phone_number'  => $user->phone_number ? $user->phone_number : '',
                    'joining_date'  => $user->joining_date ? $user->joining_date : '', 
                ]);
            }
            $userList = [
                'data'          => $userData,
                'current_page'  => $data->currentPage(),
                'total_record'  => $data->total(),
                'has_more_pages'=> $data->hasMorePages(),
            ];
            
            return $this->apiResponse('success', '200', 'User List '. config('constants.SUCESS.FETCH_DONE'), $userList); 
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage(), $e->getLine(),$e);
        }  
    }
    /* End Method getList */

    /*
    Method Name:    detail
    Developer:      Skillskore
    Purpose:        To update the user detail
    Params:
    */
    public function detail(Request $request, $id){
        try{
            $userId = decryptData($id);
            if ($request->isMethod('get')) {
                $userData = [];
                $user = User::find($userId);
                if($user){
                    $userData = [
                        'user_name'             => $user->user_name,
                        'first_name'            => $user->first_name,
                        'last_name'             => $user->last_name,
                        'email'                 => $user->email,
                        'phone_number'          => $user->phone_number ? $user->phone_number : '',
                        'joining_date'          => $user->joining_date ? $user->joining_date : '',
                        'address'               => $user->userdetail ? ($user->userdetail->address  ? $user->userdetail->address : '') : '',
                        'city'                  => $user->userdetail ? ($user->userdetail->city  ? $user->userdetail->city : '') : '',
                        'zip_code'              => $user->userdetail ? ($user->userdetail->zip_code  ? $user->userdetail->zip_code : '') : '',

                        'crypto_id'             => $user->cryptodetail ? ($user->cryptodetail->crypto_id  ? $user->cryptodetail->crypto_id : '') : '',
                        'crypto_image'          => $user->cryptodetail ? ($user->cryptodetail->crypto_image  ? $user->cryptodetail->crypto_image : '') : '',

                        'bank_name'             => $user->bankdetail ? ($user->bankdetail->bank_name  ? $user->bankdetail->bank_name : '') : '',
                        'account_number'        => $user->bankdetail ? ($user->bankdetail->account_number  ? $user->bankdetail->account_number : '') : '',
                        'ifsc_code'             => $user->bankdetail ? ($user->bankdetail->ifsc_code  ? $user->bankdetail->ifsc_code : '') : '',
                        'account_holder_name'   => $user->bankdetail ? ($user->bankdetail->account_holder_name  ? $user->bankdetail->account_holder_name : '') : '',
                        'upi_id'                => $user->bankdetail ? ($user->bankdetail->upi_id  ? $user->bankdetail->upi_id : '') : '',
                        'account_image'         => $user->bankdetail ? ($user->bankdetail->account_image  ? $user->bankdetail->account_image : '') : '',
                    ];
                    
                }
                return $this->apiResponse('success', '200', 'User detail '. config('constants.SUCESS.FETCH_DONE'), $userData); 
            }else{

                User::where('id',$userId)->update([
                    'first_name'        => $request->first_name,
                    'last_name'         => $request->last_name,
                    'phone_number'      => $request->phone_number ? $request->phone_number : '',

                ]);

                UserDetail::updateOrCreate(['user_id' => $userId],[
                    'address'   => $request->address ? $request->address : '',
                    'city'      => $request->city ? $request->city : '',
                    'zip_code'  => $request->zip_code ? $request->zip_code : '',
                ]);

                $imageData = '';
                if($request->filled('crypto_image')){
                    $image = $request->file('crypto_image');
                    $imageData = file_get_contents($image->getRealPath());
                }

                CryptoAccountDetail::updateOrCreate(['user_id' => $userId],[
                    'crypto_id'   => $request->crypto_id ? $request->crypto_id : '',
                    'crypto_image' => $imageData,
                ]);


                $imageData = '';
                if($request->filled('account_image')){
                    $image = $request->file('account_image');
                    $imageData = file_get_contents($image->getRealPath());
                }
                
                BankAccountDetail::updateOrCreate(['user_id' => $userId],[
                    'bank_name'         =>   $request->bank_name ? $request->bank_name : '',
                    'account_number'    =>   $request->account_number ? $request->account_number : '',
                    'ifsc_code'         =>   $request->ifsc_code ? $request->ifsc_code : '',
                    'account_holder_name'=>  $request->account_holder_name ? $request->account_holder_name : '',
                    'upi_id'             =>  $request->upi_id ? $request->upi_id : '',
                    'account_image'      =>  $imageData
                ]);
                return $this->apiResponse('success', '200', 'User detail '. config('constants.SUCESS.UPDATE_DONE')); 
                
            }
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
                'user_id'            => 'required', 
                'status'             => 'required|in:0,1', 
            ];
            
            $validator = Validator::make($request->all(), $validationRules);
            if ($validator->fails()) { 
                return $this->apiResponse('error', '422', $validator->errors()->first());
            } 
            User::where('id',decryptData($request->user_id))->update(['status'  => $request->status]);
            return $this->apiResponse('success', '200', 'User Status '. config('constants.SUCESS.CHANGED_DONE')); 
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage(), $e->getLine(),$e);
        }  
    }
    /* End Method changeStatus */

}
