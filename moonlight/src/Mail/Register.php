<?php

namespace Moonlight\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Moonlight\Models\User;

class Register extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->
            to($this->user->email)->
            subject('Регистрация ')->
            view('moonlight::mails.register')->with([
                'user' => $this->user,
                'password' => $this->password,
                'site' => $_SERVER['HTTP_HOST'],
		    ]);
    }
}