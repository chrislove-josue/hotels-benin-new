<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau message de contact</title>
</head>
<body style="font-family: Arial, sans-serif; color:#1A3A6B;">
    <h2 style="color:#1A3A6B;">Nouveau message reçu via le site Hôtels Bénin</h2>
    <p><strong>Nom :</strong> {{ $contactMessage->name }}</p>
    <p><strong>E-mail :</strong> {{ $contactMessage->email }}</p>
    @if($contactMessage->subject)
        <p><strong>Sujet :</strong> {{ $contactMessage->subject }}</p>
    @endif
    <p><strong>Message :</strong></p>
    <p style="white-space: pre-line;">{{ $contactMessage->message }}</p>
</body>
</html>
