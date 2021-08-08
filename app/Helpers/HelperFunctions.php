<?php


namespace App\Helpers;


use Illuminate\Support\Facades\DB;

class HelperFunctions
{

    function returnData($data, $success = true, $message = '', $code = 200)
    {

        $session_token = session('session_token', null);
        if ($success) {
            if ($message == '') {
                $message = 'Success';
            }
        } else {
            if ($message == '') {
                $message = 'Error';
            }
        }

        return response()->json([
            'session_token' => $session_token,
            'success' => $success,
            'message' => $message,
            'code' => $code,
            'data' => $data
        ],$code);
    }

    /**
     * This Method use for encrypt data
     *
     * @param $value data want to encrypt
     * @return Base64 Encoded string
     */

    function appSafeKeepDecrypt($jsonString)
    {

        $encriptionKeyToken = 'TKa%bsth$VB3U2pQmrD=Fy%!+cXnP+Qt!z@YP6LAeVFxZasrz8%55&nY2EFTbDWfd!y78YbnPgrUFLw4z@waAhm%?&jZ-4_%3xMc--Np&4zg4=g#gP-h4du7%zMct8vhE&fhaJyjLdLC@2&qcKh-6B7?y29KN_RmuLD^sL7!EtNXCAf=em+UfXzM57CUvBs$BDmr4Bz2UrYSaw$J==hTn9RqAKNuCKM2VE=QkNV5KVCLfaQ^2EKbmvn!gWne$&9r';

        $jsonString = base64_decode($jsonString);
        $jsondata = json_decode($jsonString, true);
        $salt = hex2bin($jsondata["s"]);
        $ct = base64_decode($jsondata["ct"]);
        $iv = hex2bin($jsondata["iv"]);
        $concatedPassphrase = $encriptionKeyToken . $salt;
        $md5 = array();
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 3; $i++) {
            $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
            $result .= $md5[$i];
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
        return json_decode($data, true);
    }

    /**
     * This Method use for encrypt data
     *
     * @param $value data want to encrypt
     * @return Base64 Encoded string
     */
    function appSafeKeepEncrypt($value)
    {

        $encriptionKeyToken = 'TKa%bsth$VB3U2pQmrD=Fy%!+cXnP+Qt!z@YP6LAeVFxZasrz8%55&nY2EFTbDWfd!y78YbnPgrUFLw4z@waAhm%?&jZ-4_%3xMc--Np&4zg4=g#gP-h4du7%zMct8vhE&fhaJyjLdLC@2&qcKh-6B7?y29KN_RmuLD^sL7!EtNXCAf=em+UfXzM57CUvBs$BDmr4Bz2UrYSaw$J==hTn9RqAKNuCKM2VE=QkNV5KVCLfaQ^2EKbmvn!gWne$&9r';

        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';
        $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $encriptionKeyToken . $salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
        $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
        return base64_encode(json_encode($data));
    }

    function validateRequest($validator)
    {

        /** @var Define Validate Errors */

        $validationErrors = $validator->errors()->getMessages();

        if (sizeof($validationErrors) > 0) {

            $returnData = array(

                'message' => $validationErrors,

            );

            /** Return Errors */

            return $this->returnData($returnData, false, 'Something Went Wrong (Request Params Failed)');


        }
    }

    function getDeviceId($safe_id){

        $safe_details = DB::select('SELECT * FROM device WHERE safe_id= ?', [$safe_id]);

        return $safe_details[0]->id;

    }
    function getReleaseStates($safe_id){

        $safe_details = DB::select('SELECT * FROM jobs WHERE device_id=? AND status=?;', [$safe_id,1]);

        if($safe_details==[]){
            return 1;
        }else{
            return 0;
        }


    }
    function getCurrentClientId($safe_id){

        $safe_details = DB::select('SELECT * FROM jobs WHERE device_id=? ORDER BY ID DESC LIMIT 1;', [$safe_id]);

        if($safe_details==[]){
            $safe_related_user_details = DB::select('SELECT * FROM device_has_user WHERE device_id= ? ORDER BY id ASC LIMIT 1;', [$safe_id]);
            return $safe_related_user_details[0]->user_id;
        }else{
            return $safe_details[0]->user_id;
        }

    }

    function getClientDetails($client_id){

        $client_details = DB::select('SELECT * FROM user WHERE id=?;', [$client_id]);

        return $client_details[0];

    }

    function genarateOTPNumber() {

        return mt_rand(0000, 9999);
    }

    function sendSMS($number,$msg) {

        $pno_ar = str_split($number);

        if (sizeof($pno_ar) > 11) {
            echo 'Invalid Number';
            return 'Invalid Number';
        }


        if ($pno_ar[0] == 0) {
            $pno = '94' . $pno_ar[1] . $pno_ar[2] . $pno_ar[3] . $pno_ar[4] . $pno_ar[5] . $pno_ar[6] . $pno_ar[7] . $pno_ar[8] . $pno_ar[9];
        }


//**Shout**/

        $api_key = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJjZDAzZjg4MC1mODI1LTExZWItODZkNC0wOWZkOGM1MTIxYWMiLCJzdWIiOiJTSE9VVE9VVF9BUElfVVNFUiIsImlhdCI6MTYyODQxMjY4MCwiZXhwIjoxOTQzOTQ1NDgwLCJzY29wZXMiOnsiYWN0aXZpdGllcyI6WyJyZWFkIiwid3JpdGUiXSwibWVzc2FnZXMiOlsicmVhZCIsIndyaXRlIl0sImNvbnRhY3RzIjpbInJlYWQiLCJ3cml0ZSJdfSwic29fdXNlcl9pZCI6IjEyNDg0Iiwic29fdXNlcl9yb2xlIjoidXNlciIsInNvX3Byb2ZpbGUiOiJhbGwiLCJzb191c2VyX25hbWUiOiIiLCJzb19hcGlrZXkiOiJub25lIn0.2J8OH1ztmPsF9XOWdjR6K_0YoPwcpWlzwF_tUQ2s3ro";

        $data = array(
            "source" => "ShoutDEMO",
            "transports" => array("sms"),
            "content" => array(
                "sms" =>  $msg
            ),
            "destinations" => array($pno)
        );

        $data_string = json_encode($data);
        $ch = curl_init('https://api.getshoutout.com/coreservice/messages');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key
        ));

        $result = curl_exec($ch);
        $response_sms = json_decode($result);
        curl_close($ch);

        if ($response_sms->status == "1001") {
            return 'SEND';
        } else {
            return 'FAILED';
        }



    }

}
