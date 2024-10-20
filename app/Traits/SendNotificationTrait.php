<?php

namespace App\Traits;

use App\Repositories\Contracts\NotificationContract;
use App\Jobs\SendEmailJob;
trait SendNotificationTrait
{
    public function sendNotification($data): void
    {
        $notificationData = [
            'title' => $data['title'],
            'body' => $data['body'],
            'user_id' => $data['user_id'],
            'redirect_type' => $data['redirect_type'],
            'redirect_id' => $data['redirect_id'],
            'data' => $data['data'],
            'time' => $data['time'],
            'topic' => $data['topic']
        ];
        
        app(NotificationContract::class)->create($notificationData);
    }

    public function sendNotificationToMany($data, $users): void
    {
        foreach ($users as $user) {
            $data['user_id'] = $user->id;
            $this->sendNotification($data);
        }
    }

    public function sendEmailNotification($data): void
    {
        $data['sender'] = config('mail.from.address');

        dispatch(new SendEmailJob($data));
    }
}

