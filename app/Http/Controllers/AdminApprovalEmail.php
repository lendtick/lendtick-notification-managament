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

class AdminApprovalEmail extends Controller
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
				'email_customer'    => 'required|email',
				'name_customer'    	=> 'required',
				'password_customer'	=> 'required',
				'nik_customer'		=> 'required'
			]);   

			$res = TemplateEmail::get(
				env('URL_HTML_SUCCESS_APPROVAL_ADMIN'),
				array(
					'NAME' => $request->name_customer,
					'PASSWORD' => $request->password_customer,
					'NIK' => $request->nik_customer
				)
			);

			$data = [
				'subject' => 'Email Approval Admin - Koperasi Astra',
				'body' => $res,
				'to' => $request->email_customer,
				'send_date' => date('Y-m-d H:i:s')
			];

            ## Send Email
			$send = Mail::to($request->email_customer)->send(new SendEmail($data));
			
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
