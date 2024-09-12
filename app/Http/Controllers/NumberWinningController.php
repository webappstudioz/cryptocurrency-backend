<?php

namespace App\Http\Controllers;

use App\Models\NumberWinning;
use App\Traits\SendResponseTrait;
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
                'time'      => 'required'
            ];
            
            $validator = Validator::make($request->all(), $validationRules);
            if ($validator->fails()) { 
                return $this->apiResponse('error', '422', $validator->errors()->first());
            } 
             
            foreach($request->numbers as $value){
                NumberWinning::create([
                    'user_id'       => authId(),
                    'amount'        => $value->amount,
                    'w_number'      => $value->w_number,
                    'timezone'      => 1,
                ]);
            }
             return $this->apiResponse('success', '200', 'Number '. config('constants.SUCESS.ADD_DONE')); 
         } catch(\Exception $e) {
             return $this->apiResponse('error', '400', $e->getMessage(), $e->getLine(),$e);
         }  
     }
     /* End Method store */
}
