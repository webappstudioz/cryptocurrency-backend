<?php
 
namespace App\Traits;

use App\Models\AutoResponder;
use App\Models\SmtpInformation;
use Illuminate\Support\Facades\{Password, Crypt};
use hisorange\BrowserDetect\Parser;
use Illuminate\Support\Str;
use Swift;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

trait SendResponseTrait {
    /*
    Method Name:    apiResponse
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

    /*
    Method Name:    getTemplateByName
    Purpose:        Get email template by name
    Params:         [name,id]
    */
    public function getTemplateByName($name, $id = 1) {
        $template = AutoResponder::where('template_name', $name)->first(['id', 'template_name', 'subject', 'template', 'type']);
        return $template;
   }
   /* End Method getTemplateByName */

    /*
    Method Name:    mailData
    Purpose:        prepare email data
    Params:         [$to, $subject, $email_body, $templete_name, $templete_id, $logtoken , $remarks = null]
    */   
    public function mailData($to, $subject, $email_body, $templete_name, $templete_id, $remarks = null, $user = 'User', $companyId = null, $ccs = [], $bccs = [] ){
        try{
            $stringToReplace = ['{{YEAR}}',  '{{$COMPANYNAME}}' ];
            $stringReplaceWith = [date("Y"), config('constants.COMPANYNAME') ]; 
            $email_body = str_replace( $stringToReplace , $stringReplaceWith , $email_body );
                    
            $data = [  
                'to'            => $to, 
                'subject'       => $subject,
                'html'          => $email_body, 
                'templete_name' => $templete_name,
                'templete_id'   => $templete_id,
                'remarks'       => $remarks,
                'cc'            => $ccs,
                'bcc'           => $bccs,
            ]; 

            return $data;
        } catch ( \Exception $e ) {
            throw new \Exception( $e->getMessage( ) );
        }
    } 
    /* End Method mailData */

        /*
    Method Name:    mailSend
    Purpose:        Send email from node
    Params:         [data]
    */   
    public function mailSend( $data ){
        try{ 
 
            $smtp = SmtpInformation::first();
            // $password = Crypt::decrypt($smtp->password);
            $password = $smtp->password;
            // Create the Transport
            $transport = (new Swift_SmtpTransport($smtp->host, $smtp->port, $smtp->encryption ))
            ->setUsername($smtp->username)
            ->setPassword($password);
               
            // Create the Mailer using your created Transport
            $mailer = new Swift_Mailer($transport);
            
            // Create a message
            
            $message = new Swift_Message();
            $message->setSubject($data['subject']);
            $message->setFrom([$smtp->from_email => $smtp->from_name]);
            $message->setTo($data['to']);
            
            $message->setBody($data['html'], 'text/html');
        
            // Send the message
            $result = $mailer->send($message);
            return $result;
        
        } catch ( \Exception $e ) {
            throw new \Exception( $e->getMessage( ) );
        }
    }      
    /* End Method mailSend */

}