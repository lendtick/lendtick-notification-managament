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

class OTPController extends Controller
{
	/*
	* send otp number from phone number
	*/ 
	public function send(Request $request){
		try{
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'phone_number'	=> 'required'
			]);   

			$insert_array = array(
				'OTPNumber'		=> OTPHelper::OTPNumber(),
				'PhoneNumber' 	=> $request->phone_number,
				'CreatedAt'		=> date('Y-m-d H:i:s'),
				'Campaign'		=> $request->campaign ? $request->campaign : 'register',
				'Status'		=> 1
			);

			// first_deactive
			$data_deactive = array(
				'Status'	=> 0
			);
			$deactive_otp = OTPRepo::deactive($request->phone_number, $data_deactive);
			// end deactive

			// sent OTP 
			$res = OTPRepo::create($insert_array); 

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
				'link_idcard_user'	=> 'required'
			]); 

			$res = OTPRepo::validation($request->otp_number , $request->phone_number);
			
			if (!$res>0) {
				throw new \Exception("Maaf Kode OTP Salah", 500);
			}

			if (Carbon::parse($res->CreatedAt)->addMinutes(5)->toDateTimeString() < Carbon::now()->toDateTimeString()) {
				throw new \Exception("OTP Kadaluarsa, Silahkan lakukan kirim ulang kode OTP", 500);
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


}
