<?php

namespace App\Mail;

use App\Models\VelondraStudioSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VelondraStudioContactAutoReply extends Mailable
{
    use Queueable, SerializesModels;

    public VelondraStudioSubmission $submission;

    public function __construct(VelondraStudioSubmission $submission)
    {
        $this->submission = $submission;
    }

    public function build(): self
    {
        return $this
            ->subject("We got your message, {$this->submission->name} \u{2014} Velondra Studio")
            ->view('emails.velondra_studio.auto_reply', [
                'submission' => $this->submission,
            ]);
    }
}
