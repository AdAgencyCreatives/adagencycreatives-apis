<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

function error_monitoring(
    array $exceptionData,
    string $logQuickMessage,
    string $logChannel = 'api-monitoring',
    bool $shouldNotify = true,
    string $notificationClass = BitCardApiMonitoringNotification::class,
    bool $shouldAbort = true,
    int $abortCode = 500,
    string $abortMessage = 'Something went wrong, Please try again later.'
) {
    Log::channel($logChannel)->error($logQuickMessage, $exceptionData);
    if ($shouldNotify) {
        Notification::route(TelegramChannel::class, config('services.telegram-bot-api.chat_id'))->notify(new $notificationClass($exceptionData));
    }
    if ($shouldAbort) {
        abort($abortCode, $abortMessage);
    }
}
