<?php

namespace App\Mail;

use App\Models\VelondraStudioSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class VelondraStudioContactNotification extends Mailable
{
    use Queueable, SerializesModels;

    public VelondraStudioSubmission $submission;

    public function __construct(VelondraStudioSubmission $submission)
    {
        $this->submission = $submission;
    }

    public function build(): self
    {
        $subjectLine = sprintf(
            'New Velondra Studio inquiry from %s%s',
            $this->submission->name,
            $this->submission->company ? " ({$this->submission->company})" : ''
        );

        return $this
            ->subject($subjectLine)
            ->replyTo(new Address($this->submission->email, $this->submission->name))
            ->view('emails.velondra_studio.notification', [
                'submission' => $this->submission,
            ]);
    }
}
