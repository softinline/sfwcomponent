<?php

namespace Softinline\SfwComponent\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Softinline\SfwComponent\Models\SfwSmtp;
use Softinline\SfwComponent\Services\EmailsService;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Recipient email.
     *
     * @var mixed
     */
    public $to;

    /**
     * SMTP id.
     *
     * @var mixed
     */
    public $smtpId;

    /**
     * Template key.
     *
     * @var string
     */
    public $templateKey;

    /**
     * Template data.
     *
     * @var array
     */
    public $data;

    /**
     * Locale for template translation.
     *
     * @var string|null
     */
    public $locale;

    /**
     * Extra options.
     *
     * @var array
     */
    public $options;

    /**
     * Create a new job instance.
     *
     * @param mixed $to
     * @param mixed $smtpId
     * @param string $templateKey
     * @param array $data
     * @param string|null $locale
     * @param array $options
     * @return void
     */
    public function __construct($to, $smtpId, $templateKey, $data = [], $locale = null, $options = [])
    {
        $this->to = $to;
        $this->smtpId = $smtpId;
        $this->templateKey = $templateKey;
        $this->data = $data;
        $this->locale = $locale;
        $this->options = $options;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->locale) {
            app()->setLocale($this->locale);
        }

        $smtp = null;
        if ($this->smtpId) {
            $smtp = SfwSmtp::find($this->smtpId);
        }

        EmailsService::sendNow($this->to, $smtp, $this->templateKey, $this->data, $this->options);
    }
}
