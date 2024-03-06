<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\State;
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class CommonController extends Controller
{
    use SendResponseTrait;
    /*
    Method Name:    countries
    Created Date:   2022-04-14 (yyyy-mm-dd)
    Purpose:        Get all the countries list or can filter by id or name
    Params:         optional[id, name]
    */
    public function countries(Request $request)
    {  
		$validator = Validator::make($request->all(),[ 'name' => 'string' ]);

		if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->first());
        }  

        try {
            $countries = Country::when(($request->id), function($query) use($request) {
                $query->where('id', $request->id);
            })->when(($request->name), function($query) use($request) {
                $query->where('name', $request->name);
            })->where('status', 1)->get();

            $data = []; 
            foreach($countries as $key => $country) { 
                array_push($data, [
                    'id' => encryptData($country->id),
                    'name' => $country->name,
                    'country_flag' => $country->country_flag,
                    'short_code'    =>  $country->short_code
                ]);
            } 

            return $this->apiResponse('success', '200', 'Countries list', $data); 
        } catch(\Exception $e) { 
            return $this->apiResponse('error', '400', $e->getMessage());
        } 
    } 
    /* End Method countries */

    /*  
    Method Name:    stateByCountry
    Created Date:   2022-04-14 (yyyy-mm-dd)
    Purpose:        Get all the state list country specifically
    Params:         [country_id]
    */
    public function stateByCountry(Request $request)
    {
        $validator = Validator::make($request->all(),[ 'country_id' => 'required|string' ]);

		if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->first());
        }  

        try {
            $countryId = decryptData($request->country_id);
            $states = State::where('country_id', $countryId)->get();
            $data = [];
            foreach($states as $key => $state) { 
                array_push($data, [
                    'id' => encryptData($state->id),
                    'name' => $state->name,
                    'state_code'    =>  $state->state_code
                 ]);
            } 

            return $this->apiResponse('success', '200', 'States list', $data); 
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage());
        }  
    }
    /* End Method stateByCountry */ 
}
