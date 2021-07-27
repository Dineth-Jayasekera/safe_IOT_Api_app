<?php


namespace App\Helpers;


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

}
