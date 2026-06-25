<?php

namespace App\Mail;

use App\Models\AdeospaceSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class AdeospaceContactAutoReply extends Mailable
{
    use Queueable, SerializesModels;

    public AdeospaceSubmission $submission;

    public function __construct(AdeospaceSubmission $submission)
    {
        $this->submission = $submission;
    }

    public function build(): self
    {
        return $this
            ->from(new Address('adeospace@velondra.com', 'AdeoSpace Team'))
            ->subject("We got your message, {$this->submission->name} — AdeoSpace")
            ->view('emails.adeospace.auto_reply', [
                'submission' => $this->submission,
            ]);
    }
}
