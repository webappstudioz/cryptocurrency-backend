<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                $start = $daterang[0].' 00:05:00';
                $end = $daterang[1].' 23:05:59';
            }

            $data = User::where('role_id',2)->when(!empty($start) && !empty($end) ,function($query) use($start ,$end) {
                        $query->whereBetween('created_at', [$start, $end]);
                    })->when(!empty($request->search_keyword),function($qu) use($request) {
                        $qu->where('first_name', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhere('uuid', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhere('last_name', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", '%'.$$request->search_keyword.'%')
                        ->orWhere('email', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhere('phone_number', 'like', '%'.$$request->search_keyword.'%')
                        ->orWhere('user_name', 'like', '%'.$$request->search_keyword.'%');
                    })->where('verified',1);

            $data = $data->orderBy('id','asc')->paginate(10);
            return $this->apiResponse('success', '200', 'User List '. config('constants.SUCESS.FETCH_DONE'), $data); 
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage(), $e->getLine(),$e);
        }  
    }
    /* End Method getList */
}
