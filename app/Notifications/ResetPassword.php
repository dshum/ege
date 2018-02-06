<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('password/reset', $this->token).'?email='.urlencode($notifiable->email);
        $textUrl = url('password/reset', $this->token).'?email='.$notifiable->email;

        return (new MailMessage)
            ->subject('Сброс пароля')
            ->view('mails.resetPassword')
            ->line('Вы получили это письмо, потому что был отправлен запрос на сброс пароля на сайте &laquo;ЕГЭ по биологии&raquo;.')
            ->line('Перейдите по ссылке ниже:')
            ->action($textUrl, $url)
            ->line('Если вы не отправляли запрос, ничего делать не требуется.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
