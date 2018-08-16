<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Helpers\RestCurl;
use App\Helpers\Api;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Validator;
use App\Repositories\NotificationRepo;

class MailController extends Controller
{ 

    public function __construct(NotificationRepo $notifRepo)
    {
        $this->notifRepo = $notifRepo;
    }

     /**
    * @SWG\Post(
    *     path="/send", 
    *     description="Send Notification Lendtick",
    *     operationId="auth",
    *     consumes={"application/json"},
    *     produces={"application/json"}, 
    *     @SWG\Parameter(
    *         description="To Recieve",
    *         in="query",
    *         name="to",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="CC email",
    *         in="query",
    *         name="cc",
    *         required=false,
    *         type="string"
    *     ), 
     *     @SWG\Parameter(
    *         description="Subject Notification",
    *         in="query",
    *         name="subject",
    *         required=true,
    *         type="string"
    *     ), 
     *     @SWG\Parameter(
    *         description="Body Notification",
    *         in="query",
    *         name="body",
    *         required=true,
    *         type="string"
    *     ), 
     *     @SWG\Parameter(
    *         description="Type Notification",
    *         in="query",
    *         name="type",
    *         required=true,
    *         type="string"
    *     ), 
     *     @SWG\Parameter(
    *         description="Attachment : fullpath",
    *         in="query",
    *         name="attachment",
    *         required=false,
    *         type="string"
    *     ), 
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Notifications",
    *     tags={
    *         "Notifications"
    *     }
    * )
    * */

    public function index(){
        try{
            $status   = 1;
            $httpcode = 200;
            $data     = $this->notifRepo->all();
            $errorMsg = null;
        }catch(\Exception $e){
            $status   = 0;
            $httpcode = 400;
            $data     = null;
            $errorMsg = $e->getMessage();
        }
        return response()->json(Api::format($status, $data, $errorMsg), $httpcode);
    } 

    public function send(Request $request){

        try {
            if(empty($request->json())) throw New \Exception('Params not found', 500);

            $this->validate($request, [
                'to'            => 'required|email',
                'subject'       => 'required', 
                'body'          => 'required',
                'type'          => 'required'
            ]);  

            $data = [
                'type' => $request->type,
                'subject' => $request->subject,
                'body' => $request->body,
                'to' => $request->to,
                'attachment' => !empty($request->attachment) ? $request->attachment : null,
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
            $status   = 0; //$e->getCode() ? $e->getCode() : 500;
            $httpcode = 400;
            $data     = null;
            $errorMsg = $e->getMessage();
        } 

        return response()->json(Api::format($status, $data, $errorMsg), $httpcode); 
    }

}
