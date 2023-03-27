<?php

    namespace Softinline\SfwComponent;
    
    use Illuminate\Http\Request;

    class SfwConfig
    {

        private $_config;
        private $_file;

        /**
         * return the file name loaded
         * @return string
         */
        public function getFile() {

            return $this->_file;

        }

        /**
         * return the config loaded
         * @return array
         */
        public function getConfig() {

            return $this->_config;

        }

        /**
         * return config option
         */
        public function getParam($param) {

            return $this->_config[$param];

        }

                        
        /**
         * load config file
         * and config definition
         * @return bool;
         */
        public function load($file) {

            if(file_exists($file)) {

                $this->_file = $file;

                $this->_config = json_decode(file_get_contents($this->_file), true);
                             
            }
            else {

                \App::abort(400, 'def file ['.$file.'] not found');

            }

        }
        
    }
