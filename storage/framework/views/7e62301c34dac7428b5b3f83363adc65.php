<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation de votre mot de passe</title>
</head>
<body style="font-family: Arial, sans-serif; color:#1A3A6B; background:#FBF8F1; padding:24px;">
    <div style="max-width:520px; margin:0 auto; background:#ffffff; border-radius:12px; padding:32px; border:1px solid #eee2c9;">
        <h2 style="color:#1A3A6B; margin-top:0;">Bonjour <?php echo e($user->name); ?>,</h2>

        <p>Vous avez demandé la réinitialisation du mot de passe de votre compte Hôtels Bénin.</p>

        <p>Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe. Ce lien est valable
        <strong>60 minutes</strong> et ne peut être utilisé qu'une seule fois.</p>

        <p style="text-align:center; margin:28px 0;">
            <a href="<?php echo e($url); ?>"
               style="display:inline-block; background:#1A3A6B; color:#D4AF37; text-decoration:none;
                      font-weight:bold; padding:14px 28px; border-radius:10px;">
                Réinitialiser mon mot de passe
            </a>
        </p>

        <p>Si le bouton ne fonctionne pas, copiez-collez ce lien dans votre navigateur :</p>
        <p style="word-break:break-all;"><a href="<?php echo e($url); ?>" style="color:#F59A23;"><?php echo e($url); ?></a></p>

        <p style="margin-bottom:0;">Si vous n'êtes pas à l'origine de cette demande, ignorez cet e-mail :
        votre mot de passe reste inchangé.</p>

        <p style="margin-bottom:0; font-size:13px; color:#888;">
            L'équipe Hôtels Bénin
        </p>
    </div>
</body>
</html>
<?php /**PATH C:\Project\hotels-benin-new\resources\views\emails\reset-password.blade.php ENDPATH**/ ?>