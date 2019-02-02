<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Helpers\RestCurl;
use App\Helpers\Api;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Validator;
use App\Repositories\NotificationRepo;
use App\Helpers\TemplateEmail;

class ActivationEmail extends Controller
{ 

	public function __construct(NotificationRepo $notifRepo)
    {
        $this->notifRepo = $notifRepo;
    }

	/*
	* khusus untuk mengirim email aktifasi pengguna, berikut dengan template, yang diambil dari azure blob
	*/ 
	public function index(Request $request){
		try{
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'name'          => 'required',
				'va_number'    	=> 'required',
				'to'			=> 'required',
				'amount'		=> 'required|integer',
				'phone_number'  => 'required'
			]);   

			$res = TemplateEmail::get(
				env('URL_HTML_ACTIVATION_TEMPLATE'),
				array(
					'NAME' => $request->name,
					'AMOUNT' => number_format($request->amount),
					'VA_NUMBER' => $request->va_number
				)
			);

			#send otp
			$send_otp = array(
				'phone_number' 	=> $request->phone_number,
				'campaign' 		=> 'register'
			);

			$send_otp_action = RestCurl::exec('POST',env('URL_NOTIF').'send-otp',$send_otp);
			#end

			
			$data = [
				'subject' => 'Melanjutkan ke Pembayaran - Koperasi Astra',
				'body' => $res,
				'to' => $request->to,
				'send_date' => date('Y-m-d H:i:s')
			];

            ## Send Email
			$send = Mail::to($request->to)->send(new SendEmail($data));
			
			$this->notifRepo->create($data);
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
