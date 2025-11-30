<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #0ea5e9; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { height: 50px; }
        .content { color: #333; line-height: 1.6; }
        .amount { font-size: 24px; font-weight: bold; color: #333; margin: 20px 0; text-align: center; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 4px; color: white; font-weight: bold; font-size: 14px; }
        .approved { background-color: #10b981; } /* Vert */
        .rejected { background-color: #ef4444; } /* Rouge */
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #999; }
        .reason-box { background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; margin-top: 20px; color: #b91c1c; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=768,fit=crop,q=95/AwvDBJ4r6bcKnbVp/logo-cap-avenir-fini-mePb61Q7O5TpkZQ3.png" alt="Cap Avenir" class="logo">
        </div>

        <div class="content">
            <p>Bonjour {{ $expense->user->name }},</p>

            <p>Le statut de votre note de frais du <strong>{{ $expense->expense_date->format('d/m/Y') }}</strong> a été mis à jour par le service comptabilité.</p>

            <div style="text-align: center;">
                @if($expense->status === 'approved')
                    <span class="badge approved">✅ VALIDÉE POUR PAIEMENT</span>
                @else
                    <span class="badge rejected">❌ REFUSÉE</span>
                @endif
            </div>

            <div class="amount">
                {{ number_format($expense->amount, 2, ',', ' ') }} €
            </div>

            <p><strong>Détail :</strong> {{ $expense->type === 'mileage' ? 'Indemnités Kilométriques' : 'Achat / Matériel' }}</p>

            @if($expense->status === 'rejected' && $expense->rejection_reason)
                <div class="reason-box">
                    <strong>Motif du rejet :</strong><br>
                    {{ $expense->rejection_reason }}
                </div>
                <p>Merci de corriger votre saisie ou de contacter la comptabilité.</p>
            @endif

            <p>Cordialement,<br>L'équipe Administrative.</p>
        </div>

        <div class="footer">
            Ceci est un message automatique. Merci de ne pas répondre.
        </div>
    </div>
</body>
</html>