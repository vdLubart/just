<?php

namespace Lubart\Just\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Lubart\Just\Models\Theme;

class NewFeedback extends Notification
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
    public $message;
    
    /**
     * The block title
     *
     * @var string
     */
    public $blockTitle;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($username, $message, $blockTitle, $route)
    {
        $this->username = $username;
        $this->route = $route;
        $this->message = $message;
        $this->blockTitle = $blockTitle;
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
        return (new MailMessage)
                    ->from('info@'.\Request::server ("SERVER_NAME"), env('APP_NAME'))
                    ->subject('New Feedback on '.env('APP_NAME'))
                    ->markdown(Theme::active()->name.'.emails.email')
                    ->greeting('Hello!')
                    ->line('User '. $this->username .' left a following notification on the "'.$this->blockTitle.'" page:')
                    ->line("&nbsp;")
                    ->line("<i>".$this->message."</i>")
                    ->action('Checkout changes', url(config('app.url').'/admin/'.$this->route));
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
