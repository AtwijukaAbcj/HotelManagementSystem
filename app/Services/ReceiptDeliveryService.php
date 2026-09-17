<?php

namespace App\Services;

use App\Mail\ReceiptMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ReceiptDeliveryService
{
    public function send(
        Model $document,
        ?string $email,
        string $documentType,
        string $documentNumber,
        string $recipientName,
        string $amount,
        string $documentDate,
        string $receiptUrl,
        string $emailColumn = 'receipt_email',
    ): void {
        if (!$email) {
            $document->forceFill(['receipt_email_status' => 'not_sent'])->saveQuietly();
            return;
        }

        $document->forceFill([
            $emailColumn => $document->getAttribute($emailColumn) ?? $email,
            'receipt_email_status' => 'sending',
        ])->saveQuietly();

        try {
            Mail::to($email)->send(new ReceiptMail(
                documentType: $documentType,
                documentNumber: $documentNumber,
                recipientName: $recipientName,
                amount: $amount,
                documentDate: $documentDate,
                receiptUrl: $receiptUrl,
            ));

            $document->forceFill([
                'receipt_email_status' => 'sent',
                'receipt_sent_at' => now(),
            ])->saveQuietly();
        } catch (Throwable $exception) {
            report($exception);
            $document->forceFill(['receipt_email_status' => 'failed'])->saveQuietly();
        }
    }
}
