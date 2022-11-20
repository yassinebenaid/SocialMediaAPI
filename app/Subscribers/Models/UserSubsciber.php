<?php

namespace App\Subscribers\Models;

use App\Events\UserRegistered;
use App\Listeners\SendConfirmationEmail;
use Illuminate\Contracts\Events\Dispatcher;

class UserSubsciber
{
    public function subscribe(Dispatcher $event)
    {
        $event->listen(UserRegistered::class, SendConfirmationEmail::class);
    }
}
