<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Intervention_{{ $intervention->id }}</title>
    <style>
        /* CONFIGURATION GLOBALE DES PAGES */
        @page {
            margin-top: 140px;
            margin-bottom: 50px;
            margin-left: 0px;
            margin-right: 0px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
            color: #334155;
            margin: 0;
            padding: 0;
        }

        /* --- HEADER FIXE --- */
        .header {
            position: fixed;
            top: -140px;
            left: 0px;
            right: 0px;
            height: 140px;
            background-color: #881337;
            color: white;
            z-index: 1000;
        }

        .header-content { padding: 30px 40px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .logo-box {
            background-color: white;
            padding: 8px 15px;
            border-radius: 8px;
            display: inline-block;
        }
        .logo-img { height: 45px; width: auto; display: block; }
        .doc-title {
            text-align: right; font-size: 18px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 1px; color: #ffffff;
        }
        .doc-ref {
            text-align: right; font-size: 10px; color: rgba(255,255,255,0.8); margin-top: 5px;
        }

        /* --- FOOTER --- */
        .footer {
            position: fixed;
            bottom: -50px;
            left: 0px;
            right: 0px;
            height: 40px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            line-height: 40px;
            font-size: 9px;
            color: #94a3b8;
            z-index: 1000;
        }

        /* --- COLONNE GAUCHE (Sidebar - Page 1) --- */
        .sidebar {
            position: absolute;
            top: 0;
            left: 0;
            width: 240px;
            background-color: #f8fafc;
            padding: 30px;
            border-right: 1px solid #e2e8f0;
            height: 850px; /* Hauteur forcée pour Page 1 */
            z-index: 1;
        }

        /* --- CONTENU PRINCIPAL --- */
        .content {
            /* C'EST ICI LE CHANGEMENT : 300px -> 340px */
            /* Cela laisse 100px d'espace vide entre la sidebar (240px) et le texte */
            margin-left: 340px;

            padding: 30px 40px 0 0;
            text-align: justify;
            position: relative;
            z-index: 10;
        }

        /* Styles Typo */
        .meta-block { margin-bottom: 25px; }
        .meta-label {
            font-size: 10px; text-transform: uppercase; letter-spacing: 1px;
            color: #881337; font-weight: bold; border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px; margin-bottom: 4px; display: block;
        }
        .meta-value { font-size: 13px; font-weight: bold; color: #1e293b; display: block; }
        .meta-sub { font-size: 11px; color: #64748b; margin-top: 2px; }

        .report-title {
            font-size: 20px; color: #881337; font-weight: bold;
            margin-bottom: 30px; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9;
        }

        .parsed-content { font-size: 13px; line-height: 1.8; color: #334155; }
        .parsed-content p { margin-bottom: 20px; }
        .parsed-content strong { color: #881337; font-weight: 700; }
        .parsed-content ul { padding-left: 20px; margin-bottom: 20px; }

    </style>
</head>
<body>

    <div class="header">
        <div class="header-content">
            <table class="header-table">
                <tr>
                    <td align="left">
                        <div class="logo-box">
                            <img src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=768,fit=crop,q=95/AwvDBJ4r6bcKnbVp/logo-cap-avenir-fini-mePb61Q7O5TpkZQ3.png" class="logo-img" alt="Logo">
                        </div>
                    </td>
                    <td align="right">
                        <div class="doc-title">Compte Rendu d'Intervention</div>
                        <div class="doc-ref">Réf: INT-{{ str_pad($intervention->id, 5, '0', STR_PAD_LEFT) }} | {{ date('d/m/Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        Cap Avenir — Structure d'Action Éducative — Document confidentiel soumis au secret professionnel
    </div>

    <div class="sidebar">
        <div class="meta-block">
            <span class="meta-label">Bénéficiaire</span>
            <span class="meta-value">{{ $intervention->client->full_name }}</span>
            @if($intervention->client->birth_date)
                <div class="meta-sub">Né(e) le {{ $intervention->client->birth_date->format('d/m/Y') }}</div>
            @endif
        </div>

        <div class="meta-block">
            <span class="meta-label">Éducateur Référent</span>
            <span class="meta-value">{{ $intervention->user->name }}</span>
            <div class="meta-sub">{{ $intervention->user->email }}</div>
        </div>

        <div class="meta-block">
            <span class="meta-label">Date</span>
            <span class="meta-value">{{ $intervention->start_at->format('d/m/Y') }}</span>
        </div>

        <div class="meta-block">
            <span class="meta-label">Horaires</span>
            <span class="meta-value">
                {{ $intervention->start_at->format('H:i') }} - {{ $intervention->end_at->format('H:i') }}
            </span>
            <div class="meta-sub" style="margin-top:5px;">
                <span style="background:#fce7f3; color:#881337; padding:3px 8px; border-radius:4px; font-weight:bold;">
                    Durée : {{ $intervention->duration_minutes }} min
                </span>
            </div>
        </div>

        <div class="meta-block">
            <span class="meta-label">Lieu & Type</span>
            <span class="meta-value">{{ $intervention->location_type }}</span>
            <div class="meta-sub">{{ $intervention->intervention_type }}</div>
        </div>

        <div style="margin-top: 60px; border-top: 1px dashed #cbd5e1; padding-top: 10px;">
            <span class="meta-label" style="border:none; color: #94a3b8;">Signature</span>
        </div>
    </div>

    <div class="content">
        <div class="report-title">Synthèse de l'intervention</div>

        <div class="parsed-content">
            {!! $parsedReport !!}
        </div>

        @if($intervention->raw_notes)
        <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #f1f5f9; color: #94a3b8; font-size: 10px; font-style: italic;">
            <strong>Notes brutes :</strong><br>
            {{ Str::limit($intervention->raw_notes, 300) }}
        </div>
        @endif
    </div>

</body>
</html>