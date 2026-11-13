<?php

namespace Softinline\SfwComponent\Services;

class SmtpsService
{
    /**
     * Register mail configuration from an SMTP record.
     *
     * @param mixed $smtp
     * @return bool
     */
    public static function register($smtp)
    {
        if (!$smtp) {
            \Log::info(__FUNCTION__.' | Error in object');
            return false;
        }

        $fromEmail = $smtp->from_email ?? $smtp->smtp_user ?? null;
        $fromName = $smtp->from_name ?? null;

        $encryption = self::resolveEncryption($smtp);

        if ((int) $smtp->type === \Softinline\SfwComponent\Models\SfwSmtp::SMTP) {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => $smtp->smtp_host,
                'mail.mailers.smtp.port' => $smtp->smtp_port,
                'mail.mailers.smtp.username' => !empty($smtp->smtp_user) ? $smtp->smtp_user : null,
                'mail.mailers.smtp.password' => !empty($smtp->smtp_password) ? $smtp->smtp_password : null,
                'mail.mailers.smtp.encryption' => $encryption,
                'mail.from.address' => $fromEmail,
                'mail.from.name' => $fromName ?: config('mail.from.name'),
            ]);

            // Legacy keys compatibility
            config([
                'mail.driver' => 'smtp',
                'mail.host' => $smtp->smtp_host,
                'mail.port' => $smtp->smtp_port,
                'mail.username' => !empty($smtp->smtp_user) ? $smtp->smtp_user : null,
                'mail.password' => !empty($smtp->smtp_password) ? $smtp->smtp_password : null,
                'mail.encryption' => $encryption,
            ]);
        } elseif ((int) $smtp->type === \Softinline\SfwComponent\Models\SfwSmtp::AMAZON) {
            config([
                'mail.default' => 'ses',
                'mail.mailers.ses.transport' => 'ses',
                'mail.driver' => 'ses',
                'mail.host' => $smtp->aws_host,
                'mail.port' => $smtp->aws_port,
                'mail.username' => $smtp->aws_user,
                'mail.password' => $smtp->aws_password,
                'mail.encryption' => $encryption,
                'services.ses.key' => $smtp->aws_key,
                'services.ses.secret' => $smtp->aws_secret,
                'services.ses.region' => $smtp->aws_region,
                'mail.from.address' => $fromEmail,
                'mail.from.name' => $fromName ?: config('mail.from.name'),
            ]);
        }

        return true;
    }

    /**
     * Resolve Laravel mail encryption value from SFW SMTP encryption setting.
     *
     * @param mixed $smtp
     * @return string|null
     */
    private static function resolveEncryption($smtp)
    {
        $value = null;

        if ((int) $smtp->type === \Softinline\SfwComponent\Models\SfwSmtp::SMTP) {
            $value = $smtp->smtp_encryption;
        } elseif ((int) $smtp->type === \Softinline\SfwComponent\Models\SfwSmtp::AMAZON) {
            $value = $smtp->aws_encryption;
        }

        if ($value === null || $value === '' || (int) $value === 1) {
            return null;
        }

        if ((int) $value === 2) {
            return 'tls';
        }

        if ((int) $value === 3) {
            return 'ssl';
        }

        return null;
    }
}
