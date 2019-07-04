<?php

namespace Lubart\Just\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Lubart\Just\Models\Theme;

class NewRegistration extends Notification
{
    use Queueable;
    
    /**
     * The username in the message
     *
     * @var string
     */
    public $username;
    
    /**
     * The route to the notification
     *
     * @var string
     */
    public $route;
    
    /**
     * The message text
     *
     * @var string
     */
    public $comment;
    
    /**
     * Related event
     *
     * @var string
     */
    public $event;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($username, $event, $comment, $route)
    {
        $this->username = $username;
        $this->route = $route;
        $this->comment = $comment;
        $this->event = $event;
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
        $mail = (new MailMessage)
                    ->from('info@'.\Request::server ("SERVER_NAME"), env('APP_NAME'))
                    ->subject('New Registration in the "'.$this->event->subject . '"')
                    ->markdown(Theme::active()->name.'.emails.email')
                    ->greeting('Hello!')
                    ->line('User '. $this->username .' registered in the "'.$this->event->subject . '" event' . (!empty($this->comment) ? ' and left following comment:' : '.'));
        if(!empty($this->comment)){
            $mail->line("&nbsp;")
                ->line("<i>".$this->comment."</i>");
        }

        $mail->action('Checkout changes', url(config('app.url').'/admin/'.$this->route));

        return $mail;
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
