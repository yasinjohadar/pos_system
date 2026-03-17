<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklySummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $startDate,
        public string $endDate,
        public int $salesCount,
        public float $salesTotal,
        public int $purchasesCount,
        public float $purchasesTotal
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ملخص المبيعات والمشتريات الأسبوعي - ' . $this->startDate . ' إلى ' . $this->endDate,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-summary',
        );
    }
}
