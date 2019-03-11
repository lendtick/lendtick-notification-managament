<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Helpers\RestCurl;
use App\Helpers\Api;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Validator;
use App\Repositories\NotificationRepo;
use App\Helpers\FCM;

class PushNotif extends Controller
{ 

	public function __construct()
	{
		$this->key_fcm = env('KEY_FCM');
	}

	public function index(Request $request){
		try {

			$this->validate($request, [
				'id_user'          => 'required|integer'
			]);

			$body = array(
				'title' => 'Lutfi Ngetest', 
				'body' 	=> 'Aku padamu sayang ?',
			);


			$res = FCM::individu($request->id_user , $body); 

			if ($res) {
				$status   = 1;
				$httpcode = 200;
				$data     = '';
				$errorMsg = 'Berhasil'; 
			}

		}catch(\Exception $e){
			$status   = 0;
			$httpcode = 400;
			$data     = null;
			$errorMsg = $e->getMessage();
		}
		return response()->json(Api::format($status, $data, $errorMsg), $httpcode);
	}  

}
