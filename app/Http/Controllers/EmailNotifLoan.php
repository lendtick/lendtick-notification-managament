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
use App\Helpers\FCM;

class EmailNotifLoan extends Controller
{ 

	public function __construct(NotificationRepo $notifRepo)
	{
		$this->notifRepo = $notifRepo;
	}


	## Email Credit 1
	public function sendEmail(Request $request){
		try{
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'to'				=> 	'required|email',
				'id_loan'			=>	'required',
				'status_workflow'	=>	'required',
				'push_member'		=>	'required',
				'id_user'			=>  'required',
				'status'			=>  'required'
			]);   

			if (strtolower($request->status) == 'reject') {

				$res = TemplateEmail::get(
					env('URL_HTML_REJECT_LOAN'),
					array(
						'LOAN_NUMBER' 	=> $request->id_loan,
					)
				);
				$data = [
					'subject' => 'LOAN NUMBER '.$request->id_loan . ' di REJECT',
					'body' => $res,
					'to' => $request->to,
					'send_date' => date('Y-m-d H:i:s')
				];
				$send = Mail::to($request->to)->send(new SendEmail($data));

				if (strtolower($request->push_member) == 'true') {
					$body = array(
						'title' => 'Koperasi Astra', 
						'body' 	=> 'Pinjaman kamu ditolak'
					);
					$res = FCM::individu($request->id_user , $body); 
				}

			} elseif (strtolower($request->status) == 'approve') {

				$res = TemplateEmail::get(
					env('URL_HTML_APPROVE_LOAN'),
					array(
						'LOAN_NUMBER' 	=> $request->id_loan,
					)
				);
				$data = [
					'subject' => 'Mohon untuk melakukan approval dengan nomor LOAN NUMBER :  '.$request->id_loan,
					'body' => $res,
					'to' => $request->to,
					'send_date' => date('Y-m-d H:i:s')
				];
				$send = Mail::to($request->to)->send(new SendEmail($data));

				if (strtolower($request->push_member) == 'true') {
					$body = array(
						'title' => 'Koperasi Astra', 
						'body' 	=> 'Pinjaman kamu disetujui'
					);
					$res = FCM::individu($request->id_user , $body); 
				}
				
			} else {
				throw New \Exception('Status Undefined', 500);
			}
			
			$this->notifRepo->create($data);
			$status   = 1;
			$httpcode = 200;
			$data     = '';  
			$errorMsg = 'Berhasil Kirim';

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
				'email_credit_two'	=> 	'required|email',
				'loan_number'		=>	'required',
				'name_customer'		=>	'required',
				'id_customer'		=>	'required',
				'status'			=>  'required'
			]);   

			if ($request->status == 'reject') {

				$res = TemplateEmail::get(
					env('URL_HTML_FAILED_LOAN_BY_CREDIT_2'),
					array(
						'USER' 			=> $request->name_customer,
						'LOAN_NUMBER' 	=> $request->loan_number,
					)
				);
				$data = [
					'subject' => 'Approval Pinjaman Kredit 2 - Koperasi Astra | loan number '.$request->loan_number,
					'body' => $res,
					'to' => $request->email_credit_two,
					'send_date' => date('Y-m-d H:i:s')
				];
				$send = Mail::to($request->email_credit_two)->send(new SendEmail($data));

				$body = array(
					'title' => 'Koperasi Astra', 
					'body' 	=> 'Pinjaman kamu ditolak'
				);

				$res = FCM::individu($request->id_customer , $body); 

			} elseif ($request->status == 'approve') {

				$res = TemplateEmail::get(
					env('URL_HTML_APPROVE_LOAN_BY_CREDIT_2'),
					array(
						'USER' 			=> $request->name_customer,
						'LOAN_NUMBER' 	=> $request->loan_number,
					)
				);
				$data = [
					'subject' => 'Approval Pinjaman Kredit 2 - Koperasi Astra | loan number '.$request->loan_number,
					'body' => $res,
					'to' => $request->email_credit_two,
					'send_date' => date('Y-m-d H:i:s')
				];
				$send = Mail::to($request->email_credit_two)->send(new SendEmail($data));

				$body = array(
					'title' => 'Koperasi Astra', 
					'body' 	=> 'Pinjaman kamu disetujui'
				);

				$res = FCM::individu($request->id_customer , $body); 
				
			} else {
				throw New \Exception('Status Undefined', 500);
			}
			
			$this->notifRepo->create($data);
			$status   = 1;
			$httpcode = 200;
			$data     = '';  
			$errorMsg = 'Berhasil Kirim';

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
				'body' => "Email to HR | loan number ".$request->loan_number,
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
