<?php

    namespace Softinline\SfwComponent\Services;
    
    use Softinline\SfwComponent;
        
    class SmtpsService
    {

        /**
         * register
         * sets the config to send emails
         */
        public static function register($smtp) {

            if ($smtp) {

                if($smtp->type == \Softinline\SfwComponent\Models\SfwSmtp::SMTP) {
                                                            
                    $config = [
                        'driver' => 'smtp',
                        'host' => $smtp->smtp_host,
                        'port' => $smtp->smtp_port,
                        'username' => !empty($smtp->smtp_user) ? $smtp->smtp_user : null,
                        'password' => !empty($smtp->smtp_password) ? $smtp->smtp_password : null,
                        'encryption' => $smtp->smtp_encryption == 1 ? null : 'tls',
                        'from_address' => $smtp->smtp_user,
                        'verify_peer' => false,
                    ];

                    \Config::set('mail', $config);
                    //extract(\Config::get('mail'));

                }
                
                elseif($smtp->type == \Softinline\SfwComponent\Models\SfwSmtp::AMAZON) {

                    config(['mail.driver' => 'ses']);
                    config(['mail.host' => $smtp->aws_host]);
                    config(['mail.port' => $smtp->aws_port]);
                    config(['mail.username' => $smtp->aws_user]);
                    config(['mail.password' => $smtp->aws_password]);                
                    config(['mail.encryption' => $smtp->aws_encryption == 1 ? 'null' : 'tls']);
                    config(['mail.verify_peer' => false]);
                    config(['services.ses.key' =>  $smtp->aws_key]);
                    config(['services.ses.secret' =>  $smtp->aws_secret]);
                    config(['services.ses.region' =>  $smtp->aws_region]);

                }
                                                
            } 
            else {
                
                \Log::info(__FUNCTION__.' | Error in object');
                
            }
            
            return true;

        }
        
        /**
         * getDefault
         * return the default smtp config
         */
        public static function getDefault() {

            $item = \Softinline\SfwComponent\Models\SfwSmtp::select()
                ->where('default', '=', 1)
                ->first();

            return $item;

        }

    }