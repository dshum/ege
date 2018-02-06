<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class Register extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		$url = route('register.activate', [
            'token' => $this->user->remember_token,
            'email' => $this->user->email,
        ]);

        return $this->
            to($this->user->email)->
            subject('Подтверждение регистрации')->
            view('mails.register')->with([
			    'url' => $url
		    ]);
    }
}