<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidRealEmail implements ValidationRule
{
    /**
     * Domaines de boîtes e-mail jetables / temporaires (mails de test).
     */
    protected array $disposableDomains = [
        'mailinator.com', 'yopmail.com', 'yopmail.fr', 'yopmail.net', 'getnada.com',
        'temp-mail.org', 'temp-mail.io', '10minutemail.com', 'guerrillamail.com',
        'sharklasers.com', 'maildrop.cc', 'trashmail.com', 'trashmail.ws', 'tempmail.com',
        'tempmailo.com', 'mailnesia.com', 'dispostable.com', 'mytemp.email',
        'throwaway.email', 'mintemail.com', 'spam4.me', 'tmpmail.org', 'dropmail.me',
        'emailondeck.com', 'tempinbox.com', 'mailcatch.com', 'mailsac.com',
        'fakeinbox.com', 'fake-mail.net', 'moakt.com', 'emltmp.com', 'maileater.com',
        'mytempdir.com', 'wegwerfmail.de', 'jetable.org', 'spamgourmet.com',
        'inboxbear.com', 'burnermail.io', 'tempail.com', 'fakemail.net',
        'mail-temp.com', '1secmail.com', 'mailtemp.net', 'inboxkitten.com',
        'tempinmail.com', 'mailsac.io', 'mailto.plus', 'emailfake.com', 'emailnator.com',
        'fakemailgenerator.com', 'generator.email', 'mailtemp.info', 'tmp-mail.org',
        'mytempmail.com', 'throttlemail.com', 'developermail.com', 'mailhero.io',
        'spam.la', 'spambox.us', 'trashymail.com', 'nada.email', 'kuku.li',
        'suio.com', 'emailcorner.net', 'jet-renovation.fr', 'fleckens.hu',
        'testinbox.cc', 'testmail.io', 'testmail.app', 'mailsac.com', 'captchabox.com',
        'neverbox.com', '24hourmail.com', 'inbox.si', 'radiobox.io', 'rmqkr.net',
        'jaydeecyber.com', 'mildin.org.ua', 'whgesknd.ga', 'h8s.org', 'mailr76.com',
        'safetymail.info', 'jifup.com', 'valemail.net', 'aavome.com',
    ];

    public function validate($attribute, $value, Closure $fail): void
    {
        $email = strtolower(trim((string) $value));

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $fail("L'adresse e-mail n'est pas au bon format.");

            return;
        }

        $domain = strtolower(substr(strrchr($email, '@'), 1));

        if ($domain === '') {
            $fail("L'adresse e-mail n'est pas au bon format.");

            return;
        }

        // Blocage des boîtes jetables / temporaires (mails de test).
        if (in_array($domain, $this->disposableDomains, true)) {
            $fail('Les adresses e-mail temporaires ou jetables ne sont pas autorisées. Utilisez une adresse e-mail réelle.');

            return;
        }

        // Le domaine doit posséder un enregistrement MX (serveur de courrier réel).
        if (! $this->hasMxRecord($domain)) {
            $fail("Le domaine « {$domain} » n'existe pas ou n'accepte pas de courrier.");
        }
    }

    protected function hasMxRecord(string $domain): bool
    {
        $records = @dns_get_record($domain, DNS_MX);

        return is_array($records) && count($records) > 0;
    }
}