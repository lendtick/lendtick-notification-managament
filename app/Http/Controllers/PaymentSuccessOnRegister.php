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
use App\Http\Controllers\SMSAfterReg as SMS;

class PaymentSuccessOnRegister extends Controller
{ 
	private $sms;

	public function __construct(NotificationRepo $notifRepo , SMS $sms)
    {
        $this->notifRepo = $notifRepo;
        $this->sms = $sms;
    }

	/*
	* khusus untuk mengirim email aktifasi pengguna, berikut dengan template, yang diambil dari azure blob
	*/ 
	public function index(Request $request){
		try{
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'email_customer'	=> 'required|email',
				'name_customer'    	=> 'required',
				// 'email_hrd'			=> 'required',
				'amount'			=> 'required|integer',
				'va_number'			=> 'required',
				'phone_number'		=> 'required'
			]);   

			## send email to customer
			$res = TemplateEmail::get(
				env('URL_HTML_PAYMENT_SUCCESS'),
				array(
					'NAME' => $request->name_customer,
					'AMOUNT' => number_format($request->amount),
					'VA_NUMBER' => $request->va_number
				)
			);
			$data = [
				'subject' => 'Segera Lakukan Pembayaran - Koperasi Astra',
				'body' => $res,
				'to' => $request->email_customer,
				'send_date' => date('Y-m-d H:i:s')
			];
			$send = Mail::to($request->email_customer)->send(new SendEmail($data));
			## end send email to customer 

			## send sms va
			$param_send_sms = array(
				'phone_number'	=>(string) $request->phone_number,
				'va_number'	=> (string) $request->va_number,
				'amount'	=> (string) $request->amount
			);
			$response_sms = RestCurl::exec('POST', env('URL_NOTIF').'send-sms-va-billing' , $param_send_sms, '');
			// $response_sms = $this->sms->va($param_send_sms);
			## end send sms va

			// dd([$response_sms , @$send]);
			

			## send email to HR
			// $res_hrd = TemplateEmail::get(
			// 	env('URL_HTML_NEED_APPROVAL_HR'),
			// 	array(
			// 		'EMAIL' => $request->email_customer,
			// 		'AMOUNT' => number_format($request->amount),
			// 		'VA_NUMBER' => $request->va_number
			// 	)
			// );
			// $data = [
			// 	'subject' => 'Hai HR, Mohon untuk melakukan Approval - Koperasi Astra',
			// 	'body' => $res_hrd,
			// 	'to' => $request->email_hrd,
			// 	'send_date' => date('Y-m-d H:i:s')
			// ];
			// $send = Mail::to($request->email_hrd)->send(new SendEmail($data));
			## end email to HR

            ## Send Email
			
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
