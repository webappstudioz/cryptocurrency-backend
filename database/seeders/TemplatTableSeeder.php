<?php

namespace Database\Seeders;

use App\Models\AutoResponder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TemplatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AutoResponder::updateOrCreate([
            'template_name' => 'Email_Address_Verification'
        ],[
            'template' => '<!doctype html>
            <html>
                <head>
                    <title>{{$companyName}}</title>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="x-apple-disable-message-reformatting">
                    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
                    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&amp;display=swap" rel="stylesheet" />
                    <!--–[if mso]-->
                    <style type="text/css">body, td,p {
                        font-family: Helvetica, Arial, sans-serif !important;
                        }
                    </style>
                </head>
                <body>
                    <table style="margin: auto;background:#f5f5f5;" role="presentation" border="0" cellspacing="0" cellpadding="0" align="center" width="600">
                        <tbody>
                            <tr>
                                <td style="padding: 1.5em 2.5em 1.5em 2.5em; background-color: #b8c2d2;" valign="top" align="center">
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="230">
                                        <tbody>
                                            <tr>
                                                
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="center" style="background-color: #b8c2d2;padding:0px 10px 0;">
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="540">
                                        <tbody>
                                            <tr>
                                                <td valign="top" style="padding: 0em 2em 1em;background:#ffffff;" valign="middle">
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <!-- end tr -->
                            <tr>
                                <td style="padding: 0em 2em 1.5em; background:#f5f5f5;" valign="middle">
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="540">
                                        <tbody>
                                            <tr>
                                                <td valign="top" style="padding: 0em 2.5em 2em;background:#ffffff;">
                                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td valign="top" style="text-align: left; color: #000000; font-size: 18px; padding-top: 0; line-height: 1.4; font-weight: bold;font-family: Helvetica, Arial, sans-serif;">
                                                                    Hello {{$name}},
                                                                </td>
                                                            </tr>
                                                             <tr>
                                                                <td valign="top" style="text-align: left; color: #000000; font-size: 16px; padding:15px 0 15px; line-height: 1.4; font-weight: 400;font-family: Helvetica, Arial, sans-serif;">
                                                                    Welcome to {{$COMPANYNAME}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" style="text-align: left; color: #000000; font-size: 16px; padding:15px 0 15px; line-height: 1.4; font-weight: 400;font-family: Helvetica, Arial, sans-serif;">
                                                                    Please Click on the link below to verify your email address. This is required to confirm ownership of the email address.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" style="padding: 5px 0 20px;">
                                                                    <a style="background-color: #b8c2d2; text-decoration:none;padding: 8px 20px; color: #fff;font-family: Helvetica, Arial, sans-serif;width:100px;height:32px;" width="100" height="32" href="{{$token}}">Click here</a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" style="text-align: left; color: #000000; font-size: 16px; padding-top: 0px; line-height: 1.4; font-weight: 400;font-family: Helvetica, Arial, sans-serif;word-break: break-all;">
                                                                    <b>Or Copy the below link on your browser tab :</b><br>{{$token}}
                                                                </td>
                                                            </tr>
                                                             <tr>
                                                                <td valign="top" style="text-align: left; color: #000000; font-size: 16px; padding-top: 0px; line-height: 1.4; font-weight: 400;font-family: Helvetica, Arial, sans-serif;word-break: break-all;">
                                                                    <p style="text-align: left;">Verification OTP:- {{$otp}}</p>
                                                                </td>
                                                            </tr>
                                        
                                                            <tr>
                                                                <td valign="top" style="text-align: left; color: #000000; font-size: 16px; padding-top: 0px; line-height: 1.4; font-weight: 400;font-family: Helvetica, Arial, sans-serif;word-break: break-all;">
                                                                    <p style="text-align: left;">The link is valid for 60 minutes only. If it has expired, login to our client area request a new link. </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" style="text-align: left; color: #000000; font-size: 16px; padding-top: 30px; line-height: 1.4; font-weight: 400;font-family: Helvetica, Arial, sans-serif;">
                                                                    Thanks<br><strong>{{$COMPANYNAME}} Team</strong>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td height="30" style="height:30px;"><img src="{{$logToken}}" alt=""></td>
                            </tr>
                            <tr>
                                <td style="padding: 15px 20px 15px;background:#b8c2d2;" align="center">
                                    <p style="margin: 0; font-size: 12px;font-family: Helvetica, Arial, sans-serif;">&copy; {{YEAR}} <a style="color: #141637;" >{{$COMPANYNAME}}</a>. All Rights Reserved</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </body>
            </html>',
            'subject' => 'Email Address Verification',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        AutoResponder::updateOrCreate([
            'template_name' => 'THANKS_EMAIL'
        ],[
            'template' => '<!doctype html>
            <html>
                <head>
                    <title>{{$companyName}}</title>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="x-apple-disable-message-reformatting">
                    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
                    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&amp;display=swap" rel="stylesheet" />
                    <!--–[if mso]-->
                    <style type="text/css">body, td,p {
                        font-family: Helvetica, Arial, sans-serif !important;
                        }
                    </style>
                </head>
                <body>
                    <table style="margin: auto;background:#f5f5f5;" role="presentation" border="0" cellspacing="0" cellpadding="0" align="center" width="600">
                        <tbody>
                            <tr>
                                <td style="padding: 1.5em 2.5em 1.5em 2.5em; background-color: #b8c2d2;" valign="top" align="center">
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="230">
                                        <tbody>
                                            <tr>
                                                
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="center" style="background-color: #b8c2d2;padding:0px 10px 0;">
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="540">
                                        <tbody>
                                            <tr>
                                                <td valign="top" style="padding: 0em 2em 1em;background:#ffffff;" valign="middle">
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <!-- end tr -->
                            <tr>
                                <td style="padding: 0em 2em 1.5em; background:#f5f5f5;" valign="middle">
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="540">
                                        <tbody>
                                            <tr>
                                                <td valign="top" style="padding: 0em 2.5em 2em;background:#ffffff;">
                                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td valign="top" style="text-align: left; color: #000000; font-size: 18px; padding-top: 0; line-height: 1.4; font-weight: bold;font-family: Helvetica, Arial, sans-serif;">
                                                                    Hello {{$name}},
                                                                </td>
                                                            </tr>
                                                             <tr>
                                                                <td valign="top" style="text-align: left; color: #000000; font-size: 16px; padding:15px 0 15px; line-height: 1.4; font-weight: 400;font-family: Helvetica, Arial, sans-serif;">
                                                                    Welcome to {{$COMPANYNAME}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" style="text-align: left; color: #000000; font-size: 16px; padding:15px 0 15px; line-height: 1.4; font-weight: 400;font-family: Helvetica, Arial, sans-serif;">
                                                                    Thank you for successfully verifying your email address. Please log in using your username and email address via the link below
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" style="padding: 5px 0 20px;">
                                                                    <a style="background-color: #b8c2d2; text-decoration:none;padding: 8px 20px; color: #fff;font-family: Helvetica, Arial, sans-serif;width:100px;height:32px;" width="100" height="32" href="{{$token}}">Log In</a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" style="text-align: left; color: #000000; font-size: 16px; padding-top: 0px; line-height: 1.4; font-weight: 400;font-family: Helvetica, Arial, sans-serif;word-break: break-all;">
                                                                    <b>Or Copy the below link on your browser tab :</b><br>{{$token}}
                                                                </td>
                                                            </tr>
                                                             <tr>
                                                                <td valign="top" style="text-align: left; color: #000000; font-size: 16px; padding-top: 0px; line-height: 1.4; font-weight: 400;font-family: Helvetica, Arial, sans-serif;word-break: break-all;">
                                                                    <p style="text-align: left;">User Name:- {{$user_name}}</p>
                                                                </td>
                                                            </tr>
                                        
                                                            
                                                            <tr>
                                                                <td valign="top" style="text-align: left; color: #000000; font-size: 16px; padding-top: 30px; line-height: 1.4; font-weight: 400;font-family: Helvetica, Arial, sans-serif;">
                                                                    Thanks<br><strong>{{$COMPANYNAME}} Team</strong>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td height="30" style="height:30px;"><img src="{{$logToken}}" alt=""></td>
                            </tr>
                            <tr>
                                <td style="padding: 15px 20px 15px;background:#b8c2d2;" align="center">
                                    <p style="margin: 0; font-size: 12px;font-family: Helvetica, Arial, sans-serif;">&copy; {{YEAR}} <a style="color: #141637;" >{{$COMPANYNAME}}</a>. All Rights Reserved</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </body>
            </html>',
            'subject' => 'Thanks For Email Address Verification',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
