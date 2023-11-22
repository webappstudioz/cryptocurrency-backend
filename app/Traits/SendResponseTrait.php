<?php
 
namespace App\Traits;

use Illuminate\Support\Facades\{Password, Crypt};
use hisorange\BrowserDetect\Parser;
use Illuminate\Support\Str;

trait SendResponseTrait {
    /*
    Method Name:    apiResponse
    Created Date:   2022-06-09 (yyyy-mm-dd)
    Purpose:        To send an api response
    Params:         [apiResponse,statusCode,message,data]
    */ 
    public function apiResponse($apiResponse, $statusCode = '404', $message = 'No records Found', $data = []) {
        $responseArray = [];
        if($apiResponse == 'success') {
            $responseArray['api_response'] = $apiResponse;
            $responseArray['status_code'] = $statusCode;
            $responseArray['message'] = $message;
            $responseArray['data'] = $data;
        } else {
            $responseArray['api_response'] = 'error';
            $responseArray['status_code'] = $statusCode;
            $responseArray['message'] = $message;
            $responseArray['data'] = $data;    
        }

        return response()->json($responseArray, $statusCode);
    }
    /* End Method apiResponse */


}