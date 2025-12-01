<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateLastLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // On met à jour la date sans toucher au champ "updated_at"
        $event->user->timestamps = false;
        $event->user->update([
            'last_login_at' => now()
        ]);
    }
}