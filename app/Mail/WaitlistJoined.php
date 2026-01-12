<?php

namespace App\Mail;

use App\Models\WaitlistEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WaitlistJoined extends Mailable
{
    use Queueable, SerializesModels;

    public WaitlistEntry $entry;

    /**
     * Create a new message instance.
     */
    public function __construct(WaitlistEntry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this
            ->subject('Welcome to the Clouvie waitlist')
            ->view('emails.waitlist_joined');
    }
}
