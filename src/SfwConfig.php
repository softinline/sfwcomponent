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

        /**
         * getByInternalId
         * search in json file for component with internalId
         * NOTE: internalId is no the Id used in html code
         */
        public function getByInternalId($internalId) {

            return $this->_getByInternalId($this->_config['components'], $internalId);
                                                                        
        }

        /**
         * _getByInternalId
         * private funciont doing the magic recursive
         */
        private function _getByInternalId($components, $internalId) {

            foreach($components as $component) {
                
                // check if current element has the field
                if(isset($component['internalId'])) {

                    if($component['internalId'] == $internalId) {

                        return $component;

                    }                    

                }

                // if not has, then check if has subcomponents
                if(isset($component['components'])) {
                                                                                                        
                    return $this->_getByInternalId($component['components'], $internalId);
                                                                                                
                }

            }
                                    
            return false;

        }

        /**
         * getByInternalId
         * search in json file for component with internalId
         * NOTE: internalId is no the Id used in html code
         */
        public function getByTypeAndField($type, $field) {

            return $this->_getByTypeAndField($this->_config['components'], $type, $field);
                                                                        
        }

        /**
         * _getByTypeAndField
         * do the magic         
         */
        private function _getByTypeAndField($components, $type, $field) {

            foreach($components as $component) {

                if($component['type'] == $type) {
                
                    // check if current element has the field
                    if(isset($component['field'])) {

                        if($component['field'] == $field) {

                            return $component;

                        }                    

                    }

                }

                // if not has, then check if has subcomponents
                if(isset($component['components'])) {
                                                                                                        
                    return $this->_getByTypeAndField($component['components'], $type, $field);
                                                                                                
                }

            }
                                    
            return false;

        }

        /**
         * getByInternalId
         * search in json file for component with internalId
         * NOTE: internalId is no the Id used in html code
         */
        public function getByField($field) {

            return $this->_getByField($this->_config['components'], $field);
                                                                        
        }

        /**
         * getByField
         * this is used to search component with specific fieldId, normally
         * text or textarea components for a form
         */
        private function _getByField($components, $field) {

            foreach($components as $component) {
                
                // check if current element has the field
                if(isset($component['field'])) {

                    if($component['field'] == $field) {

                        return $component;

                    }                    

                }

                // if not has, then check if has subcomponents
                if(isset($component['components'])) {
                                                                                                        
                    return $this->_getByField($component['components'], $field);
                                                                                                
                }

            }
                                    
            return false;

        }

        /**
         * setByInternalId
         */
        public function setByInternalId($internalId, $arr) { 
            
            $this->_setByInternalId($this->_config, $internalId, $arr);

        }

        /**
         * _setByInternalId
         * do the magic recursive
         */
        private function _setByInternalId(&$config, $internalId, $arr) {

            $obj = &$config;

            if(isset($obj['internalId'])) {

                if($obj['internalId'] == $internalId) {

                    $obj = $arr;
                    
                }
                elseif(isset($obj['components'])) {

                    foreach($obj['components'] as $componentKey => $componentValue) {                

                        $this->_setByInternalId($obj['components'][$componenKey], $internalId, $arr);
                        
                    }

                }
                
            }
            else {

                if(isset($obj['components'])) {

                    foreach($obj['components'] as $componenKey => $componentValue) {                

                        $this->_setByInternalId($obj['components'][$componenKey], $internalId, $arr);
                        
                    }
                 
                }
                
            }

        }
        
    }
