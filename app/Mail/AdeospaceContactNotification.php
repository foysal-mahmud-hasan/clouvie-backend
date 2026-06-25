<?php

namespace App\Mail;

use App\Models\AdeospaceSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class AdeospaceContactNotification extends Mailable
{
    use Queueable, SerializesModels;

    public AdeospaceSubmission $submission;

    public function __construct(AdeospaceSubmission $submission)
    {
        $this->submission = $submission;
    }

    public function build(): self
    {
        $subjectLine = sprintf(
            'New AdeoSpace inquiry from %s%s',
            $this->submission->name,
            $this->submission->organisation ? " ({$this->submission->organisation})" : ''
        );

        // adeospace.co.uk is not verified in Resend; send from the verified
        // velondra.com domain but present it as "AdeoSpace Team". Reply-To is
        // the submitter so hitting Reply reaches the customer directly.
        return $this
            ->from(new Address('adeospace@velondra.com', 'AdeoSpace Team'))
            ->subject($subjectLine)
            ->replyTo(new Address($this->submission->email, $this->submission->name))
            ->view('emails.adeospace.notification', [
                'submission' => $this->submission,
            ]);
    }
}
