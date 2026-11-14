<?php

namespace Softinline\SfwComponent\Services;

class EmailsService
{
    public static function send($to, $smtp, $key, $data = [], $options = [])
    {
        SmtpsService::register($smtp);

        $emailTemplate = \Softinline\SfwComponent\Models\SfwEmailTemplate::getByKey($key);

        if (!$emailTemplate) {
            \Log::info(__FUNCTION__.' | Email template not found: '.$key);
            return false;
        }

        $locale = app()->getLocale();
        $templateSubject = (string) $emailTemplate->subject;
        $templateBody = (string) $emailTemplate->body;

        $translations = \Softinline\SfwComponent\Services\TranslationsService::getTranslation(
            $emailTemplate,
            'sfw_email_templates',
            $emailTemplate->id,
            ['subject', 'body'],
            false
        );

        $templateSubject = (string) ($translations['subject'][$locale] ?? $templateSubject);
        $templateBody = (string) ($translations['body'][$locale] ?? $templateBody);

        $body = TemplateService::replaceAllBetweenChars($templateBody, '[', ']', $data);
        $subject = TemplateService::replaceAllBetweenChars($templateSubject, '[', ']', $data);

        $payload = [
            'subject' => $subject,
            'body' => $body,
        ];

        $view = $emailTemplate->layout;

        try {
            if (!$view) {
                throw new \Exception('Email template layout is missing');
            }

            \Mail::send($view, $payload, function ($message) use ($to, $payload) {
                $message->subject($payload['subject']);
                $message->to($to);
            });

            self::saveLog($smtp, $emailTemplate, $to, $subject, null, now());

            return true;
        } catch (\Throwable $e) {
            \Log::error(__FUNCTION__ . ' | ' . $e->getMessage());

            self::saveLog($smtp, $emailTemplate, $to, $subject ?? null, $e->getMessage());

            return false;
        }
    }

    /**
     * Save email send log.
     *
     * @param mixed $smtp
     * @param mixed $emailTemplate
     * @param mixed $to
     * @param string|null $subject
     * @param string|null $error
     * @param mixed $sentAt
     * @return void
     */
    private static function saveLog($smtp, $emailTemplate, $to, $subject, $error = null, $sentAt = null)
    {
        $log = new \Softinline\SfwComponent\Models\SfwEmailLog();
        $log->id = method_exists($log, 'generateSfwId') ? $log::generateSfwId() : uniqid('EL');
        $log->sfw_smtp_id = $smtp->id ?? null;
        $log->sfw_email_template_id = $emailTemplate->id ?? null;
        $log->to = is_array($to) ? implode(',', $to) : (string) $to;
        $log->subject = $subject;
        $log->status = $error ? 'error' : 'sent';
        $log->error = $error;
        $log->sent_at = $sentAt;
        $log->save();
    }
}