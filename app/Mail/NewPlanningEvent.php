<?php

namespace App\Mail;

use App\Models\Planning;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewPlanningEvent extends Mailable
{
    use Queueable, SerializesModels;

    public Planning $event;

    public function __construct(Planning $event)
    {
        $this->event = $event;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: '📅 Nouvel événement ajouté à votre agenda');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.planning_new');
    }
}