<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vos identifiants de connexion</title>
</head>
<body style="font-family: Arial, sans-serif; color:#1A3A6B; background:#FBF8F1; padding:24px;">
    <div style="max-width:520px; margin:0 auto; background:#ffffff; border-radius:12px; padding:32px; border:1px solid #eee2c9;">
        <h2 style="color:#1A3A6B; margin-top:0;">Bonjour {{ $user->name }},</h2>

        <p>Votre compte <strong>Hôtels Bénin</strong> a bien été créé. Voici vos informations de connexion :</p>

        <table style="width:100%; border-collapse:collapse; margin:20px 0; font-size:14px;">
            <tr>
                <td style="padding:8px 12px; border:1px solid #eee2c9; color:#888; width:40%;">Nom complet</td>
                <td style="padding:8px 12px; border:1px solid #eee2c9; font-weight:bold;">{{ $user->name }}</td>
            </tr>
            <tr>
                <td style="padding:8px 12px; border:1px solid #eee2c9; color:#888;">Profil</td>
                <td style="padding:8px 12px; border:1px solid #eee2c9;">
                    {{ $user->isHote() ? 'Hôte (chef d\'établissement)' : 'Voyageur' }}
                </td>
            </tr>
            <tr>
                <td style="padding:8px 12px; border:1px solid #eee2c9; color:#888;">Adresse e-mail</td>
                <td style="padding:8px 12px; border:1px solid #eee2c9; font-weight:bold;">{{ $user->email }}</td>
            </tr>
            <tr>
                <td style="padding:8px 12px; border:1px solid #eee2c9; color:#888;">Téléphone</td>
                <td style="padding:8px 12px; border:1px solid #eee2c9;">{{ $user->phone ?? '—' }}</td>
            </tr>
            <tr>
                <td style="padding:8px 12px; border:1px solid #eee2c9; color:#888;">Mot de passe</td>
                <td style="padding:8px 12px; border:1px solid #eee2c9; font-weight:bold;">{{ $password }}</td>
            </tr>
        </table>

        <p>Cliquez sur le bouton ci-dessous pour <strong>valider votre compte</strong> et accéder directement à votre espace.
        Ce lien est valable <strong>24 heures</strong> et ne peut être utilisé qu'une seule fois.</p>

        <p style="text-align:center; margin:28px 0;">
            <a href="{{ $magicUrl }}"
               style="display:inline-block; background:#1A3A6B; color:#D4AF37; text-decoration:none;
                      font-weight:bold; padding:14px 28px; border-radius:10px;">
                Valider mon compte et me connecter
            </a>
        </p>

        <p>Si le bouton ne fonctionne pas, copiez-collez ce lien dans votre navigateur :</p>
        <p style="word-break:break-all;"><a href="{{ $magicUrl }}" style="color:#F59A23;">{{ $magicUrl }}</a></p>

        <p style="margin-bottom:0;">Vous pourrez ensuite vous connecter avec votre adresse e-mail et le mot de passe ci-dessus.</p>

        <p style="margin-bottom:0; font-size:13px; color:#888; margin-top:16px;">
            L'équipe Hôtels Bénin
        </p>
    </div>
</body>
</html>
