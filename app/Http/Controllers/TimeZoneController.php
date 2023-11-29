<?php

namespace App\Http\Controllers;

use App\Models\TimeZone;
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
                    'winning_Number'    => $state->winning_number,
                    'date'              => $state->updated_at
                 ]);
            } 

            return $this->apiResponse('success', '200', 'Time Zone list', $data); 
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
            $timezones = TimeZone::where('id',decryptData($request->id))->update(['winning_number' => $request->winning_number]);
            
            return $this->apiResponse('success', '200', 'Time Zone '.config('constants.SUCCESS.UPDATE_DONE')); 
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage());
        }  
    }
    /* End Method storeTimeZone */ 
}
