<?php

namespace App\Mail;

use App\Models\Tagihan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TagihanOverdueMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tagihan $tagihan,
        public int $hariTerlambat
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'PENTING: Tagihan ' . $this->tagihan->jenis_tagihan . ' Telah Melewati Jatuh Tempo',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tagihan-overdue',
            with: [
                'tagihan' => $this->tagihan,
                'mahasiswa' => $this->tagihan->mahasiswa,
                'hariTerlambat' => $this->hariTerlambat,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
