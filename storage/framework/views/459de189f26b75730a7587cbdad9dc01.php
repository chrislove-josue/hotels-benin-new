<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau message de contact</title>
</head>
<body style="font-family: Arial, sans-serif; color:#1A3A6B;">
    <h2 style="color:#1A3A6B;">Nouveau message reçu via le site Hôtels Bénin</h2>
    <p><strong>Nom :</strong> <?php echo e($contactMessage->name); ?></p>
    <p><strong>E-mail :</strong> <?php echo e($contactMessage->email); ?></p>
    <?php if($contactMessage->subject): ?>
        <p><strong>Sujet :</strong> <?php echo e($contactMessage->subject); ?></p>
    <?php endif; ?>
    <p><strong>Message :</strong></p>
    <p style="white-space: pre-line;"><?php echo e($contactMessage->message); ?></p>
</body>
</html>
<?php /**PATH E:\Projects\hotels-benin-new\resources\views/emails/contact-message.blade.php ENDPATH**/ ?>