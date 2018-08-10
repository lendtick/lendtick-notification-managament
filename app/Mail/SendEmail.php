<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

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
       

        return $this->view('body')
                    ->with([ 'pesan' => $this->data['body']])
                    ->from('lutfi@awanesia.com','Lendtick Notifications')
                    ->subject($this->data['subject'])
                    ->to($this->data['to'])
                    // ->with([ 'message' => $this->data['message'] ]
                ;
        // $address = 'lutfi@awanesia.com';
        // $subject = 'This is a demo!';
        // $name = 'Jane Doe';

        // return $this->view('oke bang')
        //             ->from($address, $name)
        //             ->cc($address, $name)
        //             ->bcc($address, $name)
        //             ->replyTo($address, $name)
        //             ->subject($subject)
        //             );
    }
}
