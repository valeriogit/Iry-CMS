<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Notification;
use App\Notifications\PushDemo;

use App\Models\User;
use App\Models\Configuration;

class EventPushNotification extends Model
{
    use HasFactory;

    protected $table = 'eventpushnotification';
    public $timestamps = false;

    public static function RegistrationUserNotification($titolo, $messaggio, $testolink, $link=''){
        $config = Configuration::first();
        $event = EventPushNotification::first();

        /* notification only to admin*/

        if($config->webPush == 1 && $event->user == 1)
        {
            $user = User::where('is_super_admin', '=', 1)->get();
            $notify = new PushDemo($titolo, $messaggio, $testolink, $link);
            Notification::send($user,$notify);
        }
    }
}
