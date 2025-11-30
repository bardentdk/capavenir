@extends('emails.layouts.main')

@section('content')
    <h1 style="text-align:center; color:#881337;">Nouvel événement 📅</h1>

    <p>Bonjour {{ $event->user->name }},</p>
    <p>Le service comptabilité a ajouté un créneau à votre planning :</p>

    <div style="background:#f8fafc; border-left:4px solid #881337; padding:20px; margin:20px 0; border-radius:4px;">
        <p style="margin:0 0 5px 0; font-size:12px; text-transform:uppercase; color:#64748b; font-weight:bold;">Quoi</p>
        <p style="margin:0 0 15px 0; font-size:16px; font-weight:bold; color:#0f172a;">{{ $event->title }}</p>

        <p style="margin:0 0 5px 0; font-size:12px; text-transform:uppercase; color:#64748b; font-weight:bold;">Quand</p>
        <p style="margin:0; font-size:16px; color:#0f172a;">
            Le <strong>{{ $event->start_at->format('d/m/Y') }}</strong><br>
            De {{ $event->start_at->format('H:i') }} à {{ $event->end_at->format('H:i') }}
        </p>
    </div>

    <div style="text-align:center; margin-top:30px;">
        <a href="{{ route('planning.index') }}" class="btn">Voir mon agenda</a>
    </div>
@endsection