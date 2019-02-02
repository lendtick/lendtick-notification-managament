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

			$save_otp = OTPRepo::create($insert_array);

			
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
				'otp_number'	=> 'required',
				'phone_number'	=> 'required'
			]); 

			$res = OTPRepo::validation($request->otp_number , $request->phone_number);
			
			if (!$res>0) {
				throw new \Exception("Maaf Kode OTP Salah", 500);
			}

			if (Carbon::parse($res->CreatedAt)->addMinutes(5)->toDateTimeString() < Carbon::now()->toDateTimeString()) {
				throw new \Exception("OTP Kadaluarsa, Silahkan lakukan kirim ulang kode OTP", 500);
			}

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
