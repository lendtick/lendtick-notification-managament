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

class EmailNotifLoan extends Controller
{ 

	public function __construct(NotificationRepo $notifRepo)
    {
        $this->notifRepo = $notifRepo;
    }


	## Email Credit 1
	public function emailCreditOne(Request $request){
		try{
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
                'email_credit_one'	=> 'required|email',
                'loan_number'=>'required'
			]);   

			
			// $res = TemplateEmail::get(
			// 	env('URL_HTML_REJECT_APPROVAL_ADMIN'),
			// 	array(
			// 		'NAME' => $request->name_customer,
			// 		'EMAIL' => $request->email_customer
			// 	)
			// );
			$data = [
				'subject' => 'Approval Pinjaman Kredit 1 - Koperasi Astra',
				'body' => "Email to credit 1 loan number ".$request->loan_number,
				'to' => $request->email_credit_one,
				'send_date' => date('Y-m-d H:i:s')
			];
			$send = Mail::to($request->email_credit_one)->send(new SendEmail($data));
		
			
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

    ## Email Credit 2
	public function emailCreditTwo(Request $request){
		try{
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
                'email_credit_two'	=> 'required|email',
                'loan_number'=>'required'
			]);   

			
			// $res = TemplateEmail::get(
			// 	env('URL_HTML_REJECT_APPROVAL_ADMIN'),
			// 	array(
			// 		'NAME' => $request->name_customer,
			// 		'EMAIL' => $request->email_customer
			// 	)
			// );
			$data = [
				'subject' => 'Approval Pinjaman Kredit 2 - Koperasi Astra',
				'body' => "Email to credit 2 loan number ".$request->loan_number ,
				'to' => $request->email_credit_two,
				'send_date' => date('Y-m-d H:i:s')
			];
			$send = Mail::to($request->email_credit_two)->send(new SendEmail($data));
		
			
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
    
    ## Email Credit 2
	public function emailToHr(Request $request){
		try{
			if(empty($request->json())) throw New \Exception('Params not found', 500);
          
			$this->validate($request, [
                'email_hr'	=> 'required|email',
                'loan_number'=>'required'
			]);   

          
			// $res = TemplateEmail::get(
			// 	env('URL_HTML_REJECT_APPROVAL_ADMIN'),
			// 	array(
			// 		'NAME' => $request->name_customer,
			// 		'EMAIL' => $request->email_customer
			// 	)
			// );
			$data = [
				'subject' => 'Approval Pinjaman HR',
				'body' => "Email to HR loan number ".$request->loan_number,
				'to' => $request->email_hr,
				'send_date' => date('Y-m-d H:i:s')
            ];

            $send = Mail::to($request->email_credit_two)->send(new SendEmail($data));	
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
