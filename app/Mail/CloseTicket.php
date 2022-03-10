<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CloseTicket extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $ticket;
    public $closer;

    public function __construct($ticket, $closer)
    {
        $this->ticket = $ticket;
        $this->closer = $closer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
        ->subject('Closed Ticket')
        ->view('emails.ticket.close')
        ->with('ticket', $this->ticket, 'closer', $this->closer);
    }
}
