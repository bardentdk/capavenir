<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Intervention_{{ $intervention->id }}</title>
    <style>
        @page {
            margin: 0cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin-top: 3.5cm;   /* Espace pour le Header */
            margin-bottom: 2cm;  /* Espace pour le Footer */
            margin-left: 2cm;
            margin-right: 2cm;
            font-size: 13px;
            color: #374151;
            line-height: 1.6;    /* Un peu plus aéré */
        }

        /* HEADER */
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 3cm;
            background-color: #fff;
            /* border-bottom: 3px solid #0ea5e9; */
            padding: 0cm 2cm;
            padding-top: 0.5cm;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .logo-img { height: 60px; width: auto; }
        .company-info { text-align: right; font-size: 10px; color: #64748b; }

        /* FOOTER */
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1.5cm;
            background-color: #f8fafc;
            color: #94a3b8;
            text-align: center;
            line-height: 1.5cm;
            font-size: 9px;
            border-top: 1px solid #e2e8f0;
        }

        /* TITRES */
        h1 {
            color: #0f172a; font-size: 22px; margin-bottom: 2px;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .subtitle {
            color: #0ea5e9; font-weight: bold; font-size: 14px; margin-bottom: 30px;
        }

        /* TABLEAU INFOS (Reste inchangé car il tient sur une page) */
        .info-box { width: 100%; margin-bottom: 30px; border-spacing: 0; }
        .info-col { width: 48%; vertical-align: top; padding: 10px 0; }
        .label { font-weight: bold; color: #64748b; font-size: 10px; text-transform: uppercase; display: block; }
        .value { font-size: 14px; color: #0f172a; font-weight: 500; margin-bottom: 12px; display: block; border-bottom: 1px solid #f1f5f9; padding-bottom: 2px; }

        /* SECTION RAPPORT (CORRIGÉE) */
        .report-header {
            color: #0ea5e9;
            font-weight: bold;
            font-size: 16px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            margin-top: 30px;
            margin-bottom: 20px;
            page-break-after: avoid; /* Évite que le titre soit seul en bas de page */
        }

        /* Style du contenu parsé par Markdown */
        .report-content p {
            margin-bottom: 15px;
            text-align: justify;
        }
        .report-content strong {
            color: #0f172a; /* Mettre les titres en gras/noir foncé */
        }
        .report-content ul {
            margin-left: 20px;
            margin-bottom: 15px;
        }

        .badge-valid { background-color: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; border: 1px solid #bbf7d0; }
    </style>
</head>
<body>

    <header>
        <table class="header-table">
            <tr>
                <td align="left"><img src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=768,fit=crop,q=95/AwvDBJ4r6bcKnbVp/logo-cap-avenir-fini-mePb61Q7O5TpkZQ3.png" class="logo-img"></td>
                <td align="right" class="company-info">
                    <strong>CAP AVENIR</strong><br>
                    Service d'Action Éducative<br>
                    Réf: INT-{{ str_pad($intervention->id, 5, '0', STR_PAD_LEFT) }}
                </td>
            </tr>
        </table>
    </header>

    <footer>
        Cap Avenir - Document confidentiel - Page <span class="page-number"></span>
    </footer>

    <h1>Fiche d'Intervention</h1>
    <div class="subtitle">Suivi éducatif individualisé</div>

    <table class="info-box">
        <tr>
            <td class="info-col">
                <span class="label">Bénéficiaire</span>
                <span class="value">{{ $intervention->client->full_name }}</span>
                <span class="label">Lieu</span>
                <span class="value">{{ $intervention->location_type }}</span>
            </td>
            <td style="width:4%"></td>
            <td class="info-col">
                <span class="label">Éducateur</span>
                <span class="value">{{ $intervention->user->name }}</span>
                <span class="label">Date</span>
                <span class="value">{{ $intervention->start_at->format('d/m/Y') }} ({{ $intervention->duration_minutes }} min)</span>
            </td>
        </tr>
    </table>

    <div style="margin-bottom: 10px;">
        <span class="label">Statut :</span>
        @if($intervention->status === 'validated')
            <span class="badge-valid">VALIDÉ</span>
        @else
            <span style="background:#fef3c7; color:#92400e; padding:4px 8px; border-radius:4px; font-size:10px; border:1px solid #fcd34d;">PROVISOIRE</span>
        @endif
    </div>

    <div class="report-header">COMPTE RENDU D'OBSERVATION</div>

    <div class="report-content">
        {!! $parsedReport !!}
    </div>

</body>
</html>