<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\RestCurl;
use App\Helpers\Api;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Validator;
use App\Repositories\OTPRepo;
use App\Helpers\TemplateEmail;
use App\Helpers\OTP as OTPHelper;
use Carbon\Carbon;
use App\Repositories\NotificationLogResponseRepo as NotifLogRepo;

class OTPController extends Controller
{
	/*
	* send otp number from phone number
	*/ 
	public function send(Request $request){
		try{
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'phone_number'	=> 'required',
				'user_id'		=> 'required'
			]);   
			$kode = OTPHelper::OTPNumber();
			$insert_array = array(
				'OTPNumber'		=> $kode,
				'PhoneNumber' 	=> $request->phone_number,
				'CreatedAt'		=> date('Y-m-d H:i:s'),
				'Campaign'		=> $request->campaign ? $request->campaign : 'register',
				'Status'		=> 1,
				'UserId'		=> $request->user_id ? $request->user_id : 0
			);

			// first_deactive
			$data_deactive = array(
				'Status'	=> 0
			);
			$deactive_otp = OTPRepo::deactive($request->phone_number, $request->user_id, $data_deactive);
			// end deactive

			// sent OTP 
			$res = OTPRepo::create($insert_array); 

			
			$user_awo = env('AWO_USER_OTP');
			$pass_awo = env('AWO_PASSWORD_OTP');
			$sender_awo = env('AWO_SENDER_OTP');
			$phone = $request->phone_number;
			$date_send = Carbon::parse(Carbon::now())->addMinutes(-1)->format('d/m/Y H:i');
			$url = env('AWO_URL_SEND_OTP')."?user=$user_awo&pwd=$pass_awo&sender=$sender_awo&msisdn=$phone&message=$kode&description=Sms_blast&campaign=bigbike&schedule=".urlencode($date_send);
			$sendSms = $this->_curl($url);
			$logData = ['message' => $sendSms];
			NotifLogRepo::create($logData);
			// end

			 // notification to Email 
			$email_to = !empty($request->profile[0]['email']) ? $request->profile[0]['email'] : '';
			$name = !empty($request->profile[0]['name']) ? $request->profile[0]['name'] : '';
			$email = [
				"to" => $email_to,
				"cc" => '',
				"subject" => 'OTP Registrasi - Koperasi Astra Apps',
				"body" => 'Hai ' . $name . ' , ini adalah Kode OTP anda '.$kode,
				"type" => 'email',
				"attachment" => ''
			];

			$res_email = RestCurl::post(env('LINK_NOTIF','https://commerce-kai-notification.azurewebsites.net')."/send", $email);

			$status   = 1;
			$httpcode = 200;
			$data     = 'Berhasil Kirim';  
			$errorMsg = null;

		}catch(\Exception $e){
			$status   = 0;
			$httpcode = 400;
			$data     = null;
			$errorMsg = $e->getMessage();
		}
		return response()->json(Api::format($status, $data, $errorMsg), $httpcode);
	}  

	//
	public function validation(Request $request){
		try{
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'otp_number'		=> 'required',
				'phone_number'		=> 'required',
				'email_hr'			=> 'required|email',
				'user_id_hr'		=> 'required',
				'nama_hr'			=> 'required',
				'nrp_user'			=> 'required',
				'nama_user'			=> 'required',
				'perusahaan_user'	=> 'required',
				'link_idcard_user'	=> 'required',
				'user_id'			=> 'required'
			]); 

			$res = OTPRepo::validation($request->otp_number , $request->phone_number, $request->user_id);
			
			if (!$res>0) {
				throw new \Exception("Maaf Kode OTP Salah", 400);
			}

			if (Carbon::parse($res->CreatedAt)->addMinutes(5)->toDateTimeString() < Carbon::now()->toDateTimeString()) {
				throw new \Exception("OTP Kadaluarsa, Silahkan lakukan kirim ulang kode OTP", 400);
			}

			if (!OTPRepo::attempt($request->otp_number , $request->phone_number, $request->user_id)) {
				throw new \Exception("Percobaan OTP anda sudah lebih dari 5 kali, OTP tidak bisa digunakan lagi", 400);
			}


			// success 
			$res = TemplateEmail::get(
				env('URL_HTML_INFO_TO_HR_USER_REGISTER'),
				array(
					'HR_NAME'	=> $request->nama_hr,
					'EMAIL'	=> $request->nrp_user,
					'LINK_IDCARD_USER'	=> $request->link_idcard_user,
					'NRP_USER'	=> $request->nrp_user,
					'NAMA_USER'	=> $request->nama_user,
					'PERUSAHAAN_USER'	=> $request->perusahaan_user
				)
			); 
			
			$data = [
				'subject' => 'Hai HR, Pendaftaran pengguna baru - Koperasi Astra',
				'body' => $res,
				'to' => $request->email_hr,
				'send_date' => date('Y-m-d H:i:s')
			];

            ## Send Email
			$send = Mail::to($request->email_hr)->send(new SendEmail($data));


			$status   = 1;
			$httpcode = 200;
			$errorMsg = 'Berhasil Kirim';  
			$data 	  = null;

		}catch(\Exception $e){
			$status   = 0;
			$httpcode = 400;
			$data     = null;
			$errorMsg = $e->getMessage();
		}
		return response()->json(Api::format($status, $data, $errorMsg), $httpcode);

	}

	// get 
	private function _curl($url='')
	{
		$ch = curl_init();
	        // set url
		curl_setopt($ch, CURLOPT_URL, $url);
	        //return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        // $output contains the output string
		$output = curl_exec($ch);
	    // close curl resource to free up system resources
		curl_close($ch);

		return $output;
	}


}
