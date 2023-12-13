<?php

namespace App\Http\Controllers;

use App\Models\{StoreTimeZone,TimeZone};
use App\Rules\EncryptExist;
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimeZoneController extends Controller
{
    use SendResponseTrait;
    /*  
    Method Name:    getTimeZone
    Purpose:        Get time Zone
    Params:         []
    */
    public function getTimeZone(Request $request)
    {
        try {
            $timezones = TimeZone::get();
            $data = [];
            foreach($timezones as $key => $state) { 
                array_push($data, [
                    'id'                => encryptData($state->id),
                    'time_zone'         => $state->time_zone,
                 ]);
            } 

            return $this->apiResponse('success', '200', 'Time Zone list', $data); 
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage());
        }  
    }
    /* End Method getTimeZone */ 

    /*  
    Method Name:    getTimeZone
    Purpose:        Get time Zone
    Params:         []
    */
    public function getGameResults(Request $request)
    {
        try {
            $timezones = TimeZone::get();
            $data = [];
            foreach($timezones as $key => $state) { 
                array_push($data, [
                    'id'                => encryptData($state->id),
                    'time_zone'         => $state->time_zone,
                    'winning_Number'    => $state->result ? $state->result->winning_number : Null,
                    'date'              => $state->result ? $state->result->date : Null
                 ]);
            } 

            return $this->apiResponse('success', '200', 'Game results '.config('constants.SUCCESS.FETCH_DONE'), $data); 
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage());
        }  
    }
    /* End Method getTimeZone */ 

    /*  
    Method Name:    storeTimeZone
    Purpose:        store time Zone
    Params:         []
    */
    public function storeTimeZone(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'              =>  [ 'required', new EncryptExist( TimeZone::class , 'id' ) ],
            'winning_Number'  =>   'required' ,
        ]);

        if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->first());
        } 

        try {
            StoreTimeZone::updateOrCreate([
                'time_zone_id'  => decryptData($request->id),
                'user_id'       => authId(),
                'date'          => date('Y-m-d')
            ],[
                'winning_number' => $request->winning_Number
            ]);
            
            return $this->apiResponse('success', '200', 'Result '.config('constants.SUCCESS.SAVED_DONE')); 
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage());
        }  
    }
    /* End Method storeTimeZone */ 
}
