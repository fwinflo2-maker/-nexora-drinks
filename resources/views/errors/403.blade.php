<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès refusé — NEXORA</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #09090b;
            color: #fafafa;
        }
        .card {
            text-align: center;
            padding: 3rem 2rem;
            max-width: 420px;
        }
        .badge {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.35rem 0.9rem;
            border-radius: 9999px;
            border: 1px solid rgba(239,68,68,0.3);
            color: #f87171;
            background: rgba(239,68,68,0.1);
            margin-bottom: 1.5rem;
        }
        h1 { font-size: 4rem; font-weight: 800; color: #fafafa; line-height: 1; }
        p { margin-top: 0.75rem; color: #71717a; font-size: 0.95rem; line-height: 1.6; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-top: 2rem;
            padding: 0.6rem 1.4rem;
            border-radius: 0.5rem;
            background: #18181b;
            border: 1px solid #27272a;
            color: #a1a1aa;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s;
        }
        .btn:hover { border-color: #3f3f46; color: #fafafa; }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">Accès refusé</div>
        <h1>403</h1>
        <p>Vous n'avez pas les droits nécessaires pour accéder à cette ressource NEXORA.</p>
        <a href="/" class="btn">← Retourner à l'accueil</a>
    </div>
</body>
</html>
