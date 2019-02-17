<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Helpers\RestCurl;
use App\Helpers\Api;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Validator;
use App\Repositories\NotificationRepo;
use Illuminate\Support\Facades\Storage;
use App\Helpers\TemplateEmail;

class MailChangesSallary extends Controller
{ 

    public function __construct(NotificationRepo $notifRepo)
    {
        $this->notifRepo = $notifRepo;
    } 

    public function send(Request $request){

        try {
            if(empty($request->json())) throw New \Exception('Params not found', 500);

            $this->validate($request, [
                'email_hrd'     => 'required|email',
                'customer_name' => 'required',
                'nik'           => 'required',
                'member_number' => 'required',
                'attachment'    => 'required'
            ]);   

            ## send email to customer
            $res = TemplateEmail::get(
                env('URL_HTML_CHANGE_SALLARY'),
                array(
                    'CUSTOMER_NAME' => $request->customer_name,
                    'NIK' => $request->nik,
                    'MEMBER_NUMBER' => $request->member_number
                )
            );
            $data = [
                'subject' => 'Perubahan Gaji pada Pengguna - Koperasi Astra',
                'body' => $res,
                'to' => $request->email_hrd,
                'send_date' => date('Y-m-d H:i:s'),
                'attachment' => $request->attachment
            ];
            $send = Mail::to($request->email_hrd)->send(new SendEmail($data)); 
            
            $this->notifRepo->create($data);
            $status   = 1;
            $httpcode = 200;
            $data     = 'Berhasil Kirim';  
            $errorMsg = null;

        }catch(\Exception $e){
            $status   = 0; //$e->getCode() ? $e->getCode() : 500;
            $httpcode = 400;
            $data     = null;
            $errorMsg = $e->getMessage();
        } 

        return response()->json(Api::format($status, $data, $errorMsg), $httpcode); 
    }

}
