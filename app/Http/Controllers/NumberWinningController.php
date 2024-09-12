<?php

namespace App\Http\Controllers;

use App\Models\NumberWinning;
use App\Traits\SendResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NumberWinningController extends Controller
{
    use SendResponseTrait;

    /*
     Method Name:    store
     Developer:      Skillskore
     Purpose:        To store the number
     Params:
     */
     public function place(Request $request){
         try{
            
            $validationRules = [
                'numbers'   => 'required', 
            ];
            
            $validator = Validator::make($request->all(), $validationRules);
            if ($validator->fails()) { 
                return $this->apiResponse('error', '422', $validator->errors()->first());
            } 

            $balance = 2500;
            $totalAmount = array_sum(array_column($request->numbers, 'amount'));

            if ($totalAmount > $balance) {
                return $this->apiResponse('error', '400', 'Insufficient balance'); 
            }
        
           
            $timeZones = [
                ['start' => '00:00', 'end' => '11:00'],
                ['start' => '11:00', 'end' => '15:00'],
                ['start' => '15:00', 'end' => '18:00'],
                ['start' => '18:00', 'end' => '21:00'],
            ];

            $currentTime = Carbon::now()->format('H:i');
            foreach ($timeZones as $index => $range) {
                if ($currentTime >= $range['start'] && $currentTime < $range['end']) {
                   $zone_id = $index+1;
                }
            }

            foreach($request->numbers as $value){
                NumberWinning::create([
                    'user_id'       => authId(),
                    'amount'        => $value->amount,
                    'w_number'      => $value->bet_number,
                    'timezone'      => $zone_id,
                ]);
            }
             return $this->apiResponse('success', '200', 'Number '. config('constants.SUCESS.ADD_DONE')); 
         } catch(\Exception $e) {
             return $this->apiResponse('error', '400', $e->getMessage(), $e->getLine(),$e);
         }  
     }
     /* End Method store */
}
