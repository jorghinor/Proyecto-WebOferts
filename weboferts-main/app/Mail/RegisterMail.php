<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterMail extends Mailable
{
    use Queueable, SerializesModels;
    public $subject = "Bienvenido a WebOfertas!";
    public $nombres = "";
    public $email = "";
    public $hash = "";

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //return $this->view('mails.register', ["nombres"=>$this->nombres] );
        $data = (object) array(
            "nombres" => $this->nombres,
            "url" =>  url("/confirmation"."/".$this->hash),
        );
        return $this->view('mails.register')->with('mail', $data);
    }
}
