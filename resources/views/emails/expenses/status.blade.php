@extends('emails.layouts.main')

@section('content')

    @if($expense->status === 'approved')
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="width: 60px; height: 60px; background-color: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                <span style="font-size: 30px;">✅</span>
            </div>
            <h1 style="color: #166534; margin-bottom: 5px;">Note de frais validée !</h1>
            <p style="font-size: 14px; color: #166534;">Le remboursement a été programmé.</p>
        </div>
    @else
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="width: 60px; height: 60px; background-color: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                <span style="font-size: 30px;">❌</span>
            </div>
            <h1 style="color: #991b1b; margin-bottom: 5px;">Note de frais refusée</h1>
            <p style="font-size: 14px; color: #991b1b;">Une action de votre part est requise.</p>
        </div>
    @endif

    <p>Bonjour {{ $expense->user->name }},</p>
    <p>Le statut de votre demande du <strong>{{ $expense->expense_date->format('d/m/Y') }}</strong> a été mis à jour.</p>

    <table width="100%" style="margin: 25px 0; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
        <tr style="background-color: #f8fafc;">
            <td style="padding: 15px; border-bottom: 1px solid #e2e8f0; font-size: 12px; text-transform: uppercase; color: #64748b; font-weight: bold;">Type</td>
            <td style="padding: 15px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #334155; text-align: right;">
                {{ $expense->type === 'mileage' ? 'Indemnités Kilométriques' : 'Achat / Matériel' }}
            </td>
        </tr>
        <tr>
            <td style="padding: 15px; border-bottom: 1px solid #e2e8f0; font-size: 12px; text-transform: uppercase; color: #64748b; font-weight: bold;">Montant</td>
            <td style="padding: 15px; border-bottom: 1px solid #e2e8f0; font-size: 18px; color: #0f172a; font-weight: bold; text-align: right;">
                {{ number_format($expense->amount, 2, ',', ' ') }} €
            </td>
        </tr>
        @if($expense->type === 'mileage')
        <tr>
            <td style="padding: 15px; font-size: 12px; text-transform: uppercase; color: #64748b; font-weight: bold;">Trajet</td>
            <td style="padding: 15px; font-size: 14px; color: #334155; text-align: right;">
                {{ $expense->distance_km }} km
            </td>
        </tr>
        @endif
    </table>

    @if($expense->status === 'rejected' && $expense->rejection_reason)
        <div style="background-color: #fff1f2; border-left: 4px solid #be123c; padding: 15px; border-radius: 4px; margin-top: 20px;">
            <p style="color: #881337; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px;">Motif du refus :</p>
            <p style="color: #be123c; margin: 0; font-style: italic;">« {{ $expense->rejection_reason }} »</p>
        </div>
        <p style="margin-top: 20px; font-size: 14px;">Merci de corriger votre saisie ou de contacter le service comptabilité.</p>
    @endif

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ route('expenses.index') }}" class="btn">Voir mes frais</a>
    </div>

@endsection