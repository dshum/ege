<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $scope;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($scope)
    {
        $this->scope = $scope;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->scope['email'];
        $url = $this->scope['url'];

        return $this->
            to($email)->
            subject('Сброс пароля')->
            view('mails.resetPassword')->with([
			    'url' => $url
		    ]);
    }
}