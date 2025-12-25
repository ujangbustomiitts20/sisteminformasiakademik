<?php

namespace App\Mail;

use App\Models\Tagihan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TagihanReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tagihan $tagihan
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder: Tagihan ' . $this->tagihan->jenis_tagihan . ' Akan Jatuh Tempo',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tagihan-reminder',
            with: [
                'tagihan' => $this->tagihan,
                'mahasiswa' => $this->tagihan->mahasiswa,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
