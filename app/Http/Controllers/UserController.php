<?php

namespace App\Http\Controllers;

use App\Models\{BankAccountDetail,CryptoAccountDetail,User,UserDetail};
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,Validator};

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

            $status = ['inactive','active'];
            $data = User::where('role_id',2)->when(!empty($request->from) && !empty($request->to) ,function($query) use($request) {
                        $query->whereBetween('joining_date', [$request->from, $request->to]);
                    })->when(!empty($request->search_keyword),function($qu) use($request) {
                        $qu->where('first_name', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhere('last_name', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", '%'.$$request->search_keyword.'%')
                        ->orWhere('email', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhere('phone_number', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhere('user_name', 'like', '%'.$$request->search_keyword.'%');
                    })->where('verified',1)
                    ->when(!empty($request->status),function($query) use($request,$status){
                        $query->where('status',$status[$request->status]);
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
                    'country_code'  => $user->country ? '+'.$user->country->phonecode : '',
                    'joining_date'  => $user->joining_date ? $user->joining_date : '', 
                    'status'        => $user->status,
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
                        'id'                    => $id,
                        'user_name'             => $user->user_name,
                        'first_name'            => $user->first_name,
                        'last_name'             => $user->last_name,
                        'email'                 => $user->email,
                        'status'                => $user->status,
                        'phone_number'          => $user->phone_number ? $user->phone_number : '',
                        'country_code'          => $user->country ? '+'.$user->country->phonecode : '',
                        'country_id'            => $user->country ? encryptData($user->country->id) : '',
                        'joining_date'          => $user->joining_date ? $user->joining_date : '',
                        'referral_code'         => $user->referral_code,
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
                    
                }else{
                    return $this->apiResponse('error', '422', 'User id is invalid');
                }
                return $this->apiResponse('success', '200', 'User detail '. config('constants.SUCCESS.FETCH_DONE'), $userData); 
            }else{
                // return $request->file('crypto_image');
                // dd($request->all());
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
                if($request->hasFile('crypto_image')){
                    $image = $request->file('crypto_image');
                    $imageData = file_get_contents($image->getRealPath());
                }

                CryptoAccountDetail::updateOrCreate(['user_id' => $userId],[
                    'crypto_id'   => $request->crypto_id ? $request->crypto_id : '',
                    'crypto_image' => $imageData,
                ]);


                $imageData = '';
                if($request->hasFile('account_image')){
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
                return $this->apiResponse('success', '200', 'User detail '. config('constants.SUCCESS.UPDATE_DONE')); 
                
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

    /*
    Method Name:    teamList
    Developer:      Skillskore
    Purpose:        To get the list for all the team from its level
    Params:
    */
    public function teamList(Request $request, $level){
        try{
            // if(getRoleById(authId()) == config('constants.ROLES.ADMINISTRATOR')){
            //     $validator = Validator::make($request->all(), ['user_id'=> 'required']);
            //     if ($validator->fails()) { 
            //         return $this->apiResponse('error', '422', $validator->errors()->first());
            //     } 
            // }

            if(!in_array($level ,[1,2,3])){
                return $this->apiResponse('error', '422', 'Invalid level');
            }
            $userId = $request->filled('user_id') ? decryptData($request->user_id) : authId();
            
            $user = new User();
            if($level == 1){
                $user  = $user->where('supponser_by',$userId);
            }elseif($level == 2 ){
                $userIds = User::where('supponser_by',$userId)->get()->pluck('id');
                $user = $user->whereIn('supponser_by',$userIds);
            }elseif($level == 3){
                $userIds = User::where('supponser_by',$userId)->get()->pluck('id');
                $userIds = User::whereIn('supponser_by',$userIds)->get()->pluck('id');
                $user = $user->whereIn('supponser_by',$userIds);
            }

            $status = ['inactive','active'];
            $data = $user->where('role_id',2)->when(!empty($request->from) && !empty($request->to) ,function($query) use($request) {
                        $query->whereBetween('joining_date', [$request->from, $request->to]);
                    })->when(!empty($request->search_keyword),function($qu) use($request) {
                        $qu->where('first_name', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhere('last_name', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", '%'.$$request->search_keyword.'%')
                        ->orWhere('email', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhere('phone_number', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhere('user_name', 'like', '%'.$$request->search_keyword.'%');
                    })->where('verified',1)
                    ->when(!empty($request->status),function($query) use($request,$status){
                        $query->where('status',$status[$request->status]);
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
                    'country_code'  => $user->country ? '+'.$user->country->phonecode : '',
                    'joining_date'  => $user->joining_date ? $user->joining_date : '', 
                    'status'        => $user->status,
                ]);
            }
            $userList = [
                'data'          => $userData,
                'current_page'  => $data->currentPage(),
                'total_record'  => $data->total(),
                'has_more_pages'=> $data->hasMorePages(),
            ];
            
            return $this->apiResponse('success', '200', 'Team List '. config('constants.SUCCESS.FETCH_DONE'), $userList); 
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage(), $e->getLine(),$e);
        }  
    }
    /* End Method teamList */
}
