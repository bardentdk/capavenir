<?php

namespace App\Mail;

use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpenseStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public Expense $expense;

    /**
     * Create a new message instance.
     */
    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $status = $this->expense->status === 'approved' ? 'VALIDÉE' : 'REJETÉE';

        return new Envelope(
            subject: "Votre note de frais a été $status",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.expenses.status',
        );
    }
}