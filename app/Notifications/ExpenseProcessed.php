<?php

namespace App\Notifications;

use App\Models\Expense;
use App\Mail\ExpenseStatusUpdated; // On réutilise ton beau mail !
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ExpenseProcessed extends Notification
{
    use Queueable;

    public Expense $expense;

    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
    }

    /**
     * Les canaux de diffusion : Mail ET Base de données (Cloche)
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Partie EMAIL : On renvoie ton Mailable existant
     */
    public function toMail(object $notifiable)
    {
        return (new ExpenseStatusUpdated($this->expense))
                    ->to($notifiable->email);
    }

    /**
     * Partie CLOCHE (Database) : Ce qui sera stocké en JSON
     */
    public function toArray(object $notifiable): array
    {
        $statusText = $this->expense->status === 'approved' ? 'validée' : 'refusée';

        return [
            'message' => "Votre note de frais de {$this->expense->amount}€ a été $statusText.",
            'expense_id' => $this->expense->id,
            'amount' => $this->expense->amount,
            'status' => $this->expense->status,
        ];
    }
}