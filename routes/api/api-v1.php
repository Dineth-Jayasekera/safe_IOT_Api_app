<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**Defining Cors Parameters*/

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, app-token, session-token, Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

$Api_Version = 'v1';

Route::get('login-error', function () {
    return response()->json([
        'session_token' => null,
        'success' => false,
        'message' => 'login-error',
        'data' => [],
    ], 200);
});

Route::get('login-invalid', function () {
    return response()->json([
        'session_token' => null,
        'success' => false,
        'message' => 'login-invalid',
        'data' => [],
    ], 200);
});


Route::group(['middleware' => $Api_Version . '.app.auth.token'], function () use ($Api_Version) {

    /**Non Auth Routes*/

    Route::group(['prefix' => 'user'], function () use ($Api_Version) {

        Route::post('login-user', $Api_Version . '\UserManagementController@loginUser');

    });

    /**Auth Routes*/

//    Route::group(['middleware' => $Api_Version . '.login.status'], function () use ($Api_Version) {

    Route::group(['prefix' => 'user'], function () use ($Api_Version) {

        Route::get('get-details', $Api_Version . '\UserManagementController@userDetails');

    });

    Route::group(['prefix' => 'student'], function () use ($Api_Version) {

        Route::post('pre-registration-individual', $Api_Version . '\StudentManagement\StudentRegistrationController@studentPreRegistration');

    });

//    });

});
