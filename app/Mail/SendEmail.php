<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helpers\PutImage;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    { 
        if (isset($this->data['attachment'])) {

            $ImageName = time().'.pdf';
            $ResultPut = PutImage::save($this->data['attachment'], $ImageName);
            if($ResultPut) $Content = base_path().'/public/'.$ImageName;


            return $this->view('body')
            ->with([ 'pesan' => $this->data['body']])
            ->from('no-reply@koperasi-astra.com','Koperasi Astra Notifications')
            ->subject($this->data['subject'])
            ->to($this->data['to'])
            ->attach($Content, array(
                'as' => 'pdf-gaji.pdf', 
                'mime' => 'application/pdf')
            );  

        } else { 

            return $this->view('body')
            ->with([ 'pesan' => $this->data['body']])
            ->from('no-reply@koperasi-astra.com','Koperasi Astra Notifications')
            ->subject($this->data['subject'])
            ->to($this->data['to']);  
        }
        
    }
}
