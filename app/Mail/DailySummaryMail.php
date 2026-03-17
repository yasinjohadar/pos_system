<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailySummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $date,
        public int $salesCount,
        public float $salesTotal,
        public int $purchasesCount,
        public float $purchasesTotal
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ملخص المبيعات والمشتريات اليومي - ' . $this->date,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-summary',
        );
    }
}
