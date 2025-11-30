@extends('emails.layouts.main')

@section('content')
    <div style="text-align: center; margin-bottom: 30px;">
        <img src="https://img.icons8.com/clouds/200/handshake.png" alt="Welcome" width="100" style="margin-bottom: 10px;">
        <h1>Bienvenue dans l'équipe !</h1>
    </div>

    <p>Bonjour <strong>{{ $user->name }}</strong>,</p>

    <p>Nous sommes ravis de vous compter parmi nous. Votre espace personnel <strong>Cap Avenir</strong> a été créé. Il vous permettra de gérer vos interventions et vos frais en toute simplicité.</p>

    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px; margin: 30px 0; text-align: center;">
        <p style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 5px; font-weight: 600;">Votre Identifiant</p>
        <p style="font-size: 18px; color: #0f172a; font-weight: 700; margin-bottom: 20px;">{{ $user->email }}</p>

        <p style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 5px; font-weight: 600;">Mot de passe provisoire</p>
        <p style="font-size: 20px; color: #881337; font-family: monospace; font-weight: 700; margin-bottom: 0; background: #fff; display: inline-block; padding: 5px 15px; border-radius: 4px; border: 1px dashed #cbd5e1;">{{ $password }}</p>
    </div>

    <p style="text-align: center;">
        <a href="{{ route('login') }}" class="btn">Accéder à mon espace</a>
    </p>

    <p style="font-size: 13px; color: #64748b; margin-top: 30px; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 20px;">
        🔒 Pour votre sécurité, nous vous invitons à modifier ce mot de passe dès votre première connexion via la rubrique "Mon Profil".
    </p>
@endsection