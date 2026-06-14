<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Sincronizzazione Strava fallita</title>
</head>
<body style="font-family: sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #E85D04;">Sincronizzazione Strava fallita</h2>

    <p>Ciao,</p>

    <p>
        La sincronizzazione delle tue attività Strava non è riuscita perché l'autorizzazione
        del tuo account Strava non è più valida. Questo accade quando l'accesso a PEMIQ
        viene revocato dalla tua area personale Strava.
    </p>

    <p>
        Per riprendere la sincronizzazione automatica delle attività, ti chiediamo di
        ricollgare il tuo account Strava dalla dashboard.
    </p>

    <p style="margin: 30px 0;">
        <a href="{{ url('/dashboard') }}"
           style="background-color: #E85D04; color: white; padding: 12px 24px;
                  text-decoration: none; border-radius: 6px; font-weight: bold;">
            Ricollega Strava dalla Dashboard
        </a>
    </p>

    <p style="color: #666; font-size: 14px;">
        Se hai già ricollegato il tuo account, puoi ignorare questa email.
    </p>

    <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
    <p style="color: #999; font-size: 12px;">PEMIQ — Il tuo assistente per l'analisi degli allenamenti</p>
</body>
</html>
