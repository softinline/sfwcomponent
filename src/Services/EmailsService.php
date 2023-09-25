<?php

    namespace Softinline\SfwComponent\Services;
    
    use Softinline\SfwComponent;
        
    class EmailsService
    {

        /**
         * send
         */
        public static function send($to, $smtp, $key, $data) {
            
            // register the smtp config received
            \Softinline\SfwComponent\Services\SmtpsService::register($smtp);

            // get template and replace [ params ] with values and send
            $emailTemplate = \Softinline\SfwComponent\Models\SfwEmailTemplate::getByKey($key);

            if($emailTemplate) {

                // get ocurrences
                $occurrences = \Softinline\SfwComponent\Services\CommonService::findAllBetweenChars($emailTemplate->body, '[', ']');

                $body = $emailTemplate->body;

                foreach($occurrences as $occurrence) {

                    $body = str_replace('['.$occurrence.']', $data[$occurrence], $body);

                }

                $data = [
                    'subject' => $emailTemplate->subject,
                    'body' => $body,
                ];

                \Log::info(__FUNCTION__.' | Body -> '.$body);
                \Log::info(__FUNCTION__.' | Smtp -> '.print_r($smtp, true));

                // send email                
                \Mail::send('sfwcomponent::backoffice.emails.email', $data, function($message) use($to, $data, $smtp) {
                    $message->from($smtp->from_address);
                    $message->subject($data['subject']);
                    $message->to($to);
                });

            }
            else {

                \Log::info(__FUNCTION__.' | Email template not found');

            }

        }

    }