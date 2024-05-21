<?php
use Illuminate\Support\Facades\{ Auth}; 
use App\Models\{Role,User};
/*
Method Name:    encryptData
Purpose:        encrypt data
Params:         [data, encryptionMethod, secret]
*/

if (!function_exists('encryptData')) {
    function encryptData(string $data, string $encryptionMethod = null, string $secret = null)
    {
        $encryptionMethod = config('constants.encryptionMethod');
        $secret = config('constants.secrect');
        try {
            $iv = substr($secret, 0, 16);
            $jsencodeUserdata = str_replace('/', '!', openssl_encrypt($data, $encryptionMethod, $secret, 0, $iv));
            $jsencodeUserdata = str_replace('+', '~', $jsencodeUserdata);

            return $jsencodeUserdata;
        } catch (\Exception $e) { 
            return null;
        }
    }
} 
/* End Method encryptData */

/*
Method Name:    decryptData
Purpose:        Decrypt data
Params:         [data, encryptionMethod, secret]
*/  
if (!function_exists('decryptData')) {
    function decryptData(string $data, string $encryptionMethod = null, string $secret = null)
    {
        // return $data;
        $encryptionMethod = config('constants.encryptionMethod');
        $secret = config('constants.secrect');
        try {
            $iv = substr($secret, 0, 16);
            $data = str_replace('!', '/', $data);
            $data = str_replace('~', '+', $data);
            $jsencodeUserdata = openssl_decrypt($data, $encryptionMethod, $secret, 0, $iv);

            return $jsencodeUserdata;
        } catch (\Exception $e) {
           return null;
        }
    }
}
/* End Method decryptData */

/*
    Method Name:    getRoleById
    Purpose:        get role by id
    Params:         [userid]
*/  
if (!function_exists('getRoleById')) {
    function getRoleById($userId) {
        $user = User::find($userId); 
        if(  $user ){
            $role = Role::find($user->role_id) ? Role::find($user->role_id)->name : '';
            return $role;
        }
        return null;
    }
}
/* End Method getRoleById */

/*
    Method Name:    authId
    Purpose:        To get role from current login user
    Params:         ['']
*/
if (!function_exists('authId')) {
    function authId( ) {
        if( !Auth::check())
            return null; 
        else 
            return Auth::id(); 
    }
}
/* End Method authId */
?>