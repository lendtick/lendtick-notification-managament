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

class ApprovalHRDEmail extends Controller
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
				'email_customer'	=> 'required|required',
				'name_customer'    	=> 'required',
				'email_admin'		=> 'required|email'
			]);   

			## send email to customer
			$res = TemplateEmail::get(
				env('URL_HTML_SUCCESS_APPROVAL_HRD'),
				array(
					'NAME' => $request->name_customer
				)
			);
			$data = [
				'subject' => 'Approval HR Sukses - Koperasi Astra',
				'body' => $res,
				'to' => $request->email_customer,
				'send_date' => date('Y-m-d H:i:s')
			];
			$send = Mail::to($request->email_customer)->send(new SendEmail($data));
			## end send email to customer

			## send email to kop admin
			$res_hr = TemplateEmail::get(
				env('URL_HTML_NEED_APPROVAL_ADMIN'),
				array(
					'EMAIL' => $request->email_customer
				)
			);
			$data = [
				'subject' => 'Hai Koperasi Admin, Mohon untuk melakukan Approval - Koperasi Astra - Koperasi Astra',
				'body' => $res_hr,
				'to' => $request->email_admin,
				'send_date' => date('Y-m-d H:i:s')
			];
			$send = Mail::to($request->email_admin)->send(new SendEmail($data));
			## end send email to kop admin

			
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
