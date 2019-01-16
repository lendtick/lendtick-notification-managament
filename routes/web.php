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

	$router->post('/send-email-activation', 'ActivationEmail@index');
	$router->post('/send-email-approval-hrd', 'ApprovalHRDEmail@index');
	$router->post('/send-email-approval-admin', 'AdminApprovalEmail@index');
// });
