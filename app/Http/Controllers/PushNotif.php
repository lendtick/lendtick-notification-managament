<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Helpers\RestCurl;
use App\Helpers\Api;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Validator;
use App\Repositories\NotificationRepo;

class PushNotif extends Controller
{ 

	public function __construct()
	{
		$this->key_fcm = env('KEY_FCM');
	}

	public function index(Request $request){
		try{
			if(empty($request->json())) throw New \Exception('Params not found', 500);

            $this->validate($request, [
                'message'            => 'required',
                'title'       		 => 'required'
            ]);  

            $post = array(
			'to' => '/topics/global',
			'notification' => array (
				'title' => $request->title,
				'body' => $request->message
			)
			); 

			$res = RestCurl::exec('POST' , env('URL_FCM'), $post , 'key='.$this->key_fcm);


			$status   = 1;
			$httpcode = 200;
			$data     = $res['data'];
			$errorMsg = ""; 

		}catch(\Exception $e){
			$status   = 0;
			$httpcode = 400;
			$data     = null;
			$errorMsg = $e->getMessage();
		}
		return response()->json(Api::format($status, $data, $errorMsg), $httpcode);
	}  
 
}
