<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\Helpers\Api;

$router->get('/', function () use ($router) {
    $message = 'Welcome to Service Notifications';
    return response()->json(Api::format(200, $router->app->version(), []), 200);
});

$router->get('/all', 'MailController@index'); 

// $router->group(['middleware' => ['token_validate'],'prefix' => '/'], function() use ($router){
	$router->post('/send', 'MailController@send');
	$router->post('/push', 'PushNotif@index');

	$router->post('/send-email-after-register', 'ActivationEmail@index');
	$router->post('/send-email-after-payment-register', 'PaymentSuccessOnRegister@index');
	$router->post('/send-email-to-approval-admin', 'ApprovalHRDEmail@index');
	$router->post('/send-email-to-success-registration', 'AdminApprovalEmail@index');

	## ketika user register di reject oleh hr dan koperasi admin
	$router->post('/send-email-to-reject-approval-hr', 'ApprovalHRDEmail@reject');
	$router->post('/send-email-to-reject-approval-admin', 'AdminApprovalEmail@reject');

	$router->post('/send-otp', 'OTPController@send');
	$router->post('/send-otp-validate', 'OTPController@validation');


	$router->post('/send-sms-after-payment', 'SMSAfterReg@success');
	$router->post('/send-sms-va-billing', 'SMSAfterReg@va');

	// sms forgot password
	$router->post('/send-sms-forgot-password', 'SMSForgotPassword@success');

	// SMS
	$router->post('/send-email-nonactive-user', 'NonAktifUserEmail@index'); 
	$router->post('/send-email-forgot-password', 'EmailForgotPassword@index');


	// mail ganti gaji 
	$router->post('/send-info-change-sallary', 'MailChangesSallary@send');
	



// });
