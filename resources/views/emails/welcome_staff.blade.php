<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; background-color: #f8fafc; padding: 40px 0; margin: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 3px solid #0ea5e9; padding-bottom: 25px; margin-bottom: 25px; }
        .logo { height: 60px; }
        h1 { color: #0f172a; font-size: 22px; margin-top: 0; }
        p { color: #475569; line-height: 1.6; font-size: 15px; }
        .credentials-box { background-color: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 25px 0; text-align: center; }
        .label { text-transform: uppercase; font-size: 11px; color: #64748b; letter-spacing: 1px; font-weight: bold; margin-bottom: 5px; display: block; }
        .value { font-size: 18px; color: #0f172a; font-weight: bold; margin-bottom: 15px; display: block; }
        .value:last-child { margin-bottom: 0; }
        .btn { display: inline-block; background-color: #0ea5e9; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 6px; font-weight: bold; margin-top: 10px; }
        .btn:hover { background-color: #0284c7; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=768,fit=crop,q=95/AwvDBJ4r6bcKnbVp/logo-cap-avenir-fini-mePb61Q7O5TpkZQ3.png" alt="Cap Avenir" class="logo">
        </div>

        <h1>Bienvenue dans l'équipe, {{ $user->name }} ! 👋</h1>

        <p>Votre compte utilisateur a été créé avec succès. Vous pouvez dès à présent accéder à l'espace de gestion pour saisir vos interventions et vos frais.</p>

        <div class="credentials-box">
            <span class="label">Votre identifiant</span>
            <span class="value">{{ $user->email }}</span>

            <span class="label">Votre mot de passe provisoire</span>
            <span class="value">{{ $password }}</span>
        </div>

        <p style="text-align: center;">
            <a href="{{ route('login') }}" class="btn">Se connecter à mon espace</a>
        </p>

        <p style="font-size: 13px; color: #ef4444; text-align: center; margin-top: 20px;">
            ⚠️ Pour votre sécurité, pensez à modifier ce mot de passe dès votre première connexion via la page "Mon Profil".
        </p>

        <div class="footer">
            Ceci est un message automatique envoyé par l'application Cap Avenir.
        </div>
    </div>
</body>
</html>