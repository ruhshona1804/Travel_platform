<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    public function via($notifiable): array { return ['database']; }

    public function toArray($notifiable): array
    {
        return ['title' => __('ui.notif_welcome_title'), 'body' => __('ui.notif_welcome_body'), 'url' => route('planner')];
    }
}
