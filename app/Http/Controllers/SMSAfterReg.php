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
use App\Repositories\NotificationLogResponseRepo;

class SMSAfterReg extends Controller
{
	public function __construct(NotificationLogResponseRepo $notifLogRepo)
    {
        // $this->notifRepo = $notifRepo;
        $this->notifLogRepo = $notifLogRepo;
	}
	
	/*
	* send sms to register success
	*/ 
	public function success(Request $request){
		try{
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'phone_number'	=> 'required',
				'anggota_id'	=> 'required',
				'password'		=> 'required'

			]);
			
			$user_awo = env('AWO_USER_REG');
			$pass_awo = env('AWO_PASSWORD_REG');
			$sender_awo = env('AWO_SENDER_REG');
			$phone = $request->phone_number;
			$date_send = Carbon::parse(Carbon::now())->addMinutes(-1)->format('d/m/Y H:i');
			$message = 'Pendaftaran dan pembayaran kamu berhasil . No Anggota : ' .$request->anggota_id. ' katasandi : '.$request->password;
			$url = env('AWO_URL_SEND_REG')."?user=$user_awo&pwd=$pass_awo&sender=$sender_awo&msisdn=$phone&message=".urlencode($message)."&description=Sms_blast&campaign=bigbike&schedule=".urlencode($date_send);

			$sendSms = $this->_curl($url);
			$logData = ['message' => $sendSms];
			$this->notifLogRepo->create($logData);
			// end

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

	// send va sms
	public function va(Request $request){
		try{
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'phone_number'	=> 'required',
				'va_number'		=> 'required',
				'amount'		=> 'required'

			]);
			
			$user_awo = env('AWO_USER_REG');
			$pass_awo = env('AWO_PASSWORD_REG');
			$sender_awo = env('AWO_SENDER_REG');
			$phone = $request->phone_number;
			$date_send = Carbon::parse(Carbon::now())->addMinutes(-1)->format('d/m/Y H:i');
			$message = 'Silahkan lakukan pembayaran dengan nomor VA : '.$request->va_number.' Total Rp.'.$request->amount;
			$url = env('AWO_URL_SEND_REG')."?user=$user_awo&pwd=$pass_awo&sender=$sender_awo&msisdn=$phone&message=".urlencode($message)."&description=Sms_blast&campaign=bigbike&schedule=".urlencode($date_send);
			$sendSms = $this->_curl($url);
			$logData = ['message' => $sendSms];
			$this->notifLogRepo->create($logData);
			// end

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
