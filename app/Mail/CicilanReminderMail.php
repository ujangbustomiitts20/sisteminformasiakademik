<?php

namespace App\Mail;

use App\Models\DetailCicilan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CicilanReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public DetailCicilan $detailCicilan
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder: Cicilan Ke-' . $this->detailCicilan->cicilan_ke . ' Akan Jatuh Tempo',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cicilan-reminder',
            with: [
                'detail' => $this->detailCicilan,
                'cicilan' => $this->detailCicilan->cicilan,
                'mahasiswa' => $this->detailCicilan->cicilan->tagihan->mahasiswa ?? null,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
