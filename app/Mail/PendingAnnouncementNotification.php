<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PendingAnnouncementNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $announcements;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($announcements)
    {
        $this->announcements = $announcements;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Gözləmədə olan Elanlar')
            ->markdown('emails.announcement.pending');
    }
}
