<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        /* Reset CSS pour email clients */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f1f5f9; }

        /* Styles Utilitaires */
        .wrapper { width: 100%; table-layout: fixed; background-color: #f1f5f9; padding-bottom: 40px; }
        .main-table { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; font-family: sans-serif; color: #334155; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .header { background-color: #ffffff; padding: 30px 0; text-align: center; border-bottom: 4px solid #881337; }
        .content { padding: 40px 40px; }
        .footer { padding: 30px; text-align: center; font-size: 12px; color: #94a3b8; background-color: #f1f5f9; }
        .btn { display: inline-block; padding: 14px 32px; background-color: #881337; color: #ffffff !important; text-decoration: none; font-weight: bold; border-radius: 50px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; transition: background 0.3s; margin-top: 20px; }
        .btn:hover { background-color: #9f1239; }

        /* Typography */
        h1 { color: #0f172a; font-size: 24px; font-weight: 700; margin: 0 0 20px; letter-spacing: -0.5px; }
        p { font-size: 16px; line-height: 1.6; margin: 0 0 20px; color: #475569; }
        strong { color: #881337; font-weight: 600; }

        /* Mobile */
        @media screen and (max-width: 600px) {
            .content { padding: 20px !important; }
            .main-table { width: 100% !important; border-radius: 0 !important; }
        }
    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main-table" width="100%">
            <tr>
                <td class="header">
                    <img src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=768,fit=crop,q=95/AwvDBJ4r6bcKnbVp/logo-cap-avenir-fini-mePb61Q7O5TpkZQ3.png" alt="Cap Avenir" width="120" style="display: block; margin: 0 auto;">
                </td>
            </tr>

            <tr>
                <td class="content">
                    @yield('content')
                </td>
            </tr>
        </table>

        <table width="100%" style="max-width: 600px; margin: 0 auto;">
            <tr>
                <td class="footer">
                    <p style="margin-bottom: 10px; font-size: 12px;">© {{ date('Y') }} <strong>Cap Avenir</strong>. Tous droits réservés.</p>
                    <p style="margin: 0; font-size: 11px;">Ceci est un message automatique, merci de ne pas y répondre directement.</p>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>