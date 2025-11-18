<?php

namespace App\Mail;

use App\Models\DataImportLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ImportCompletedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public DataImportLog $log;

    /**
     * Create a new message instance.
     */
    public function __construct(DataImportLog $log)
    {
        $this->log = $log;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('admin@testera.com', 'Admin Tester'),
            replyTo: [
                new Address('replyto@example.com', 'Admin Tester'),
            ],
            subject: 'Import Completed'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.import_completed',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
