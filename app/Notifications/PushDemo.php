<?php

namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Models\Configuration;

class PushDemo extends Notification
{
    use Queueable;

    protected $title;
    protected $icon;
    protected $badge;
    protected $message;
    protected $textlink;
    protected $link;

    public function __construct($title, $message, $textLink, $link) {

        $config = Configuration::first();

        $this->title = $title;
        $this->message = $message;
        $this->textLink = $textLink;
        $this->link = $link;
        $this->icon = $config->iconNotification;
        $this->badge = $config->iconBadge;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush()
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->icon($this->icon)
            ->body($this->message)
            ->action($this->textLink, $this->link)
            ->badge($this->badge)
            ->options(['TTL' => 1000]);
            //->image();
    }
}
