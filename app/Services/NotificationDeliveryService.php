<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class NotificationDeliveryService
{
    public function deliver(string $channel, string $recipient, string $subject, string $message): array
    {
        return match ($channel) {
            'email' => $this->email($recipient, $subject, $message),
            'sms' => $this->twilio($recipient, $message, config('services.twilio.from')),
            'whatsapp' => $this->twilio($recipient, $message, config('services.twilio.whatsapp_from')),
            default => ['status' => 'failed', 'provider' => null, 'error' => 'Unsupported channel'],
        };
    }

    private function email(string $recipient, string $subject, string $message): array
    {
        try {
            Mail::raw($message, function ($mail) use ($recipient, $subject) {
                $mail->to($recipient)->subject($subject);
            });

            return ['status' => 'sent', 'provider' => config('mail.default')];
        } catch (\Throwable $exception) {
            return ['status' => 'failed', 'provider' => config('mail.default'), 'error' => $exception->getMessage()];
        }
    }

    private function twilio(string $recipient, string $message, ?string $from): array
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');

        if (!$sid || !$token || !$from) {
            return ['status' => 'not_configured', 'provider' => 'twilio', 'error' => 'Twilio credentials are not configured.'];
        }

        try {
            $response = Http::asForm()->withBasicAuth($sid, $token)->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'To' => $recipient,
                'From' => $from,
                'Body' => $message,
            ]);

            return $response->successful()
                ? ['status' => 'sent', 'provider' => 'twilio', 'provider_id' => $response->json('sid')]
                : ['status' => 'failed', 'provider' => 'twilio', 'error' => $response->body()];
        } catch (\Throwable $exception) {
            return ['status' => 'failed', 'provider' => 'twilio', 'error' => $exception->getMessage()];
        }
    }
}