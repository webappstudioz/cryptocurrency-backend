<?php

return [
    //Error message
    'ERROR' => [
        'OOPS_ERROR'                => 'Oops!! Something went wrong',
        'NOT_EXIST'                 => 'is not exist',
        'EXIST'                     => ' is already used',
        'TRY_AGAIN_ERROR'           => 'Oops!! Something went wrong. Try again later',
        'FORBIDDEN_ERROR'           => 'Oops!! Something went wrong',
        'INVALID_TOKEN'             => 'Invalid access token',
        'TOKEN_INVALID'             => 'This password reset token is invalid',
        'WRONG_CREDENTIAL'          => 'Please provide valid credentials',
        'PASSWORD_TOKEN_INVALID'    => 'This password token is invalid', 
        'NOT_VALID_EMAIL'           => 'Please provide a valid email',
        'ACCOUNT_ISSUE'             => 'Oops! your account is not active yet. Please verify your email or contact administrator',
        'EMAIL_ISSUE'               => 'Oops! Something went wrong. Please contact to administrator to verify',
        'IMAGE_TYPE'                => 'Please select png or jpg type image',
        'PASSWORD_MISMATCH'         => 'The current password does not match',
        'PASSWORD_SAME'             => 'The new password cannot be the same as your current password',
        'IP_ISSUE'                  => "You can't review this company due to ip confict. For more details contact administrator",
        'DELETE_ERROR'              => 'You can not delete this entry as it is associate with one of the other entry',
        'NO_AUTHORIZATION'          => 'You do not have the required authorization',
        'REQUIRED_PERMS'            => 'Required parameters are not available in the request',
        'CREATE_LEAD'               => 'First, create the lead',
        'LINK_EXPIRED'              => 'Link has been expired',
    ],
    'SUCCESS' => [
        'SCHEDULE_DONE'     => 'has been scheduled successfully',
        'ASSIGN_DONE'       => 'has been assigned successfully',
        'FETCH_DONE'        => 'has been fetched successfully',
        'UPDATE_DONE'       => 'has been updated successfully',
        'SAVED_DONE'        => 'has been saved successfully',
        'CREATE_DONE'       => 'has been created successfully',
        'ADD_DONE'          => 'has been added successfully',
        'UPLOAD_DONE'       => 'has been uploaded successfully',
        'RENAME_DONE'       => 'has been renamed successfully',
        'SENT_DONE'         => 'has been sent successfully',
        'SUBMIT_DONE'       => 'has been submitted successfully',
        'CHANGED_DONE'      => 'has been changed successfully',
        'DELETE_DONE'       => 'has been deleted successfully',
        'PASSWORD_SET'      => 'has been set successfully',
        'STATUS_UPDATE'     => 'status has been updated successfully',
        'REPLY_SENT'        => 'Reply has been sent successfully',
        'EXPIRE_DONE'       => 'has been expired',
        'RESET_LINK_MAIL'   => 'We have sent you an email with a password reset link',
        'CONTACT_DONE'      => 'You message has been sent successfully. Waiting for administrator reply',
        'WELCOME'           => 'Thank you for verifying your email',
        'LOGOUT'            => 'has been logout successfully',
        'WELCOME_LOGIN'     => 'Thank you for verifying your email. Login to your account',
        'ACCOUNT_CREATED'   => 'Welcome to Velocity, your account has been created successfully please verify your email first for login',
        'ACCOUNT_UPDATE'    => 'Welcome to Velocity, your account has been created successfully please update your details',
        'ACCOUNT_SUSPEND'   => 'Your account has been suspended on your request. Your account will be disabled from listing for 30 days. After 30 days your account will be deleted permanently, if you did not reactivate. You can reactivate your Velocity account at any time by logging back into Velocity',
    ],

    'MAX_FCS_FILE_SIZE'     => env('MAX_FCS_FILE_SIZE',30000),/** Maximum lead document in Kilobyte **/

    'ROLES'     =>  [
        'ADMINISTRATOR'         =>  env('ROLE_ADMINISTRATOR', 'Admin'),
        'USER'               =>  env('ROLE_USER', 'User'),
    ],

    'secrect'               => env('ENC_DEC_SECRET', ''),
    'encryptionMethod'      => env('ENC_DEC_METHOD', ''),
    'VALID_FILE_DOCUMENTS'  =>  "image/png,image/jpeg,image/gif,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,text/plain,application/msword,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,audio/mpeg,audio/x-wav,application/octet-stream",

    'COMPANYNAME'           => env('COMPANYNAME','C2C Pvt. Ltd.'),

    'OTP_VERIFICATION'  => env('OTP_VERIFICATION', '/otp-verification?token='),
    'VERIFICATION'      => env('VERIFICATION', '/verification/'),
    
    'LOGIN'             => env('LOGIN', '/login'),
    'FRONTEND_URL'      => env('FRONTEND_URL', 'http://localhost:3000'),

];
