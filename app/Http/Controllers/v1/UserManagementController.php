<?php

namespace App\Http\Controllers\v1;


use App\Http\Controllers\Controller;
use App\Helpers\HelperFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Claims\Expiration;
use Tymon\JWTAuth\Claims\IssuedAt;
use Tymon\JWTAuth\Claims\Issuer;
use Tymon\JWTAuth\Claims\JwtId;
use Tymon\JWTAuth\Claims\NotBefore;
use Tymon\JWTAuth\Claims\Subject;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class UserManagementController extends Controller
{


    /**
     *This Method Use for login user to system
     */
    public function loginUser()
    {

        $HelperFunctions=new HelperFunctions();

        $user_data=array(
            "email"=>"",
            "pw"=>"",
        );

        $refresh_token=$HelperFunctions->appSafeKeepEncrypt(json_encode($user_data));

        $data = [
            'userProfileID' => 1,
            'userLoginID' => 1,
            'userLoginHistoryID' => 1,
            'refresh_token' => $refresh_token,
            'userRole' => 1,
            'isAdmin' => 1,
            'iss' => new Issuer('AP'),
            'iat' => new IssuedAt(Carbon::now('UTC')),
//                                'exp' => new Expiration(Carbon::now('UTC')->addMinutes(1)),
            'exp' => new Expiration(Carbon::now('UTC')->addDays(1)),
            'nbf' => new NotBefore(Carbon::now('UTC')),
            'sub' => new Subject('AP'),
            'jti' => new JwtId('AP'),
        ];


        $customClaims = JWTFactory::customClaims($data);
        $payload = JWTFactory::make($data);
        $token = JWTAuth::encode($payload);

//                            JWT Token Start End

        session(['session_token' => $token->get()]);

        $return_data=array();
        return $HelperFunctions->returnData($return_data,true,"Successfully logged in",200);


    }

    /**
     *This Method Use for load user details
     */
    public function userDetails()
    {
        $HelperFunctions=new HelperFunctions();

//        $users = DB::table('ninegenn_sa.Users')->get();
//        $students = DB::table('ninegenn_rajarata.students')->get();

        return $HelperFunctions->returnData(array(),true,"Student Data",200);


    }



}
