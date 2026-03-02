<?php

namespace App\Listeners;

use App\Services\AuditService;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        AuditService::log('LOGIN', $event->user, [], [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
