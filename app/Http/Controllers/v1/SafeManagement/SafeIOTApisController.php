<?php

namespace App\Http\Controllers\v1\SafeManagement;

use App\Helpers\HelperFunctions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class SafeIOTApisController extends Controller
{
    function __construct()
    {

        $this->HelperFunctions = new HelperFunctions();;

    }

    public function updateDeviceLog(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'safe_id' => 'required|string',
            'longitude' => 'required|string',
            'latitude' => 'required|string',
            'rfid_uuid' => 'required|string',
            'battery_status' => 'required|integer',
            'device_status' => 'required|integer',

        ]);

        if ($validator->fails()) {
            return $this->HelperFunctions->validateRequest($validator);
        }

        $device_id = $this->HelperFunctions->getDeviceId($request->safe_id);
        $current_client_id = $this->HelperFunctions->getCurrentClientId($device_id);
        $release_states = $this->HelperFunctions->getReleaseStates($device_id);

        $status = DB::insert('INSERT INTO `device_log` (`latitude`,`longitude`,`bettery_status`,`created_on`,`device_state`,`device_id`) VALUES (?,?,?,?,?,?)',
            [$request->latitude, $request->longitude, $request->battery_status, time(), $request->device_status, $device_id]);

        if ($status == "true") {

            $data = array(
                "release_states" => $release_states,
                "current_client_id" => $current_client_id,
            );
            return $this->HelperFunctions->returnData($data, true, "successfully Logged", 200);

        }

    }


    public function askingForJob(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'safe_id' => 'required|string',
            'longitude' => 'required|string',
            'latitude' => 'required|string'

        ]);

        if ($validator->fails()) {
            return $this->HelperFunctions->validateRequest($validator);
        }

        $device_id = $this->HelperFunctions->getDeviceId($request->safe_id);

        $search_available_job = DB::select('SELECT jobs.*,user.full_name   FROM jobs INNER JOIN user ON jobs.user_id=user.id WHERE jobs.device_id=? AND jobs.status=0 ORDER BY jobs.id ASC LIMIT 1;', [$device_id]);

//        dd($search_available_job);

        if ($search_available_job != []) {
            $data = array(
                "job_available" => 1,
                "client_id" => $search_available_job[0]->user_id,
                "client_name" => $search_available_job[0]->full_name,
                "job_id" => $search_available_job[0]->id,
                "job_type" => $search_available_job[0]->type,
            );

            $update_job = DB::update('UPDATE jobs SET status=1 WHERE id=?', [$search_available_job[0]->id]);


            return $this->HelperFunctions->returnData($data, true, "Available Job Details", 200);

        } else {

            $data = array(
                "job_available" => 0,
                "client_id" => "",
                "client_name" => "",
                "job_id" => "",
                "job_type" => "",
            );

            return $this->HelperFunctions->returnData($data, true, "No Available Jobs Found", 200);

        }


    }


    public function OpenClose(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'safe_id' => 'required|string',
            'longitude' => 'required|string',
            'latitude' => 'required|string',
            'open_state' => 'required|int',
            'client_id' => 'required|int',


        ]);

        if ($validator->fails()) {
            return $this->HelperFunctions->validateRequest($validator);
        }

        $device_id = $this->HelperFunctions->getDeviceId($request->safe_id);

        $state = "close";
        if ($request->open_state == 1) {
            $state = "open";
        }

        $update_job = DB::update('UPDATE device SET state=? WHERE id=?', [$state, $device_id]);


        return $this->HelperFunctions->returnData(array(), true, "Updated", 200);


    }


    public function tamplerAleart(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'safe_id' => 'required|string',
            'longitude' => 'required|string',
            'latitude' => 'required|string'


        ]);

        if ($validator->fails()) {
            return $this->HelperFunctions->validateRequest($validator);
        }

        $device_id = $this->HelperFunctions->getDeviceId($request->safe_id);

        $current_client_id = $this->HelperFunctions->getCurrentClientId($device_id);

        $client_details = $this->HelperFunctions->getClientDetails($current_client_id);

        $msg = "Breach Detected";

        $send_sms = $this->HelperFunctions->sendSMS($client_details->phone_number, $msg);

        if ($send_sms == "SEND") {
            return $this->HelperFunctions->returnData(array(), true, "Alert Send", 200);
        }else{
            return $this->HelperFunctions->returnData(array(), true, "Alert Send Failed", 200);
        }


    }


    public function sendOtp(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'safe_id' => 'required|string',
            'rfid_uuid' => 'required|string',
            'current_client_id' => 'required|int',
            'longitude' => 'required|string',
            'latitude' => 'required|string'


        ]);

        if ($validator->fails()) {
            return $this->HelperFunctions->validateRequest($validator);
        }

        $device_id = $this->HelperFunctions->getDeviceId($request->safe_id);

        $current_client_id = $this->HelperFunctions->getCurrentClientId($device_id);

        $client_details = $this->HelperFunctions->getClientDetails($current_client_id);


        $otp = $this->HelperFunctions->genarateOTPNumber();
        $msg = "Your OTP Code Is : " . $otp;

        $send_sms = $this->HelperFunctions->sendSMS($client_details->phone_number, $msg);

        if ($send_sms == "SEND") {
            $data=array(
                "otp"=>$otp
            );
            return $this->HelperFunctions->returnData($data, true, "OTP Send", 200);
        }else{
            return $this->HelperFunctions->returnData(array(), true, "OTP Failed", 200);
        }

    }

    public function createJob(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'safe_id' => 'required|string',
            'longitude' => 'required|string',
            'latitude' => 'required|string',
            'job_type' => 'required|string',
            'rfid_uuid' => 'required|string',
            'client_id' => 'required|integer',

        ]);

        if ($validator->fails()) {
            return $this->HelperFunctions->validateRequest($validator);
        }

        $device_id = $this->HelperFunctions->getDeviceId($request->safe_id);

//        $release_states = $this->HelperFunctions->getReleaseStates($device_id);

        $status = DB::insert('INSERT INTO `jobs` (type,status,created_on,update_on,device_id,user_id) VALUES (?,?,?,?,?,?)',
            [$request->job_type, 0, time(), time(), $device_id, $request->client_id]);

        if ($status == "true") {

            return $this->HelperFunctions->returnData(array(), true, "successfully Added", 200);

        }

    }

}
