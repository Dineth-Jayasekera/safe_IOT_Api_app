<?php

namespace App\Http\Controllers\v1\StudentManagement;

use App\Helpers\HelperFunctions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class StudentRegistrationController extends Controller
{

    function __construct()
    {

        $this->HelperFunctions=new HelperFunctions();;

    }

    public function studentPreRegistration(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'customer_details' => 'required|array',
            'customer_details.customer_id' => 'required|integer',
            'customer_details.customer_email' => 'required|email',
            'customer_details.customer_fname' => 'required|string',
            'customer_details.customer_lname' => 'required|string',
            'customer_details.customer_phone' => 'string|nullable',
            'customer_details.customer_note' => 'required|string',

            'payment_details.currency' => 'required|string',
            'payment_details.payment_method' => 'required|string',
            'payment_details.payment_method_id' => 'required|integer',
            'payment_details.payment_profile_id' => 'required|integer',
            'payment_details.payment_tenor_id' => 'required|integer',

            'payment_data' => 'required|string',
            'is_order_gift' => 'boolean',

        ]);

        if ($validator->fails()) {
            return $this->HelperFunctions->validateRequest($validator);
        }
//
//        $status = DB::insert('INSERT INTO `person_data` (`name`,`address`,`lat`,`lng`,`contact_number`,`need`,`is_need_show`,`user_id`) VALUES (?,?,?,?,?,?,?,?)',
//            [$request->full_name, $request->address, $request->lat, $request->lng, $request->contact_number, $request->need, $request->is_need_show, $user_id]);
//
//        if ($status == "true") {}

    }
}
