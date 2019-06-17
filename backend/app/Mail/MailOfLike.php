<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailOfLike extends Mailable
{
    use Queueable, SerializesModels;

    public $typeLike;
    public $status;

    public function __construct($typeLike, $status)
    {
        $this->typeLike = $typeLike;
        $this->status = $status;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.MailOfLike');
    }
}
