<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPushSubscriptions;

    public function isSuperAdmin()
    {
        /**
         * Here goes your logic to determine which users are "super_admin"
         *
         * For example, in case you have a'is_super_admin' boolean column
         * in your database, you could do:
         */

         return $this->is_super_admin;
    }
}
