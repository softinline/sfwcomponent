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
         * getById
         * search in json file for component with id         
         */
        public function getById($id) {

            return $this->_getById($this->_config['components'], $id);
                                                                        
        }

        /**
         * _getById
         * private funciont doing the magic recursive
         */
        private function _getById($components, $id) {
            
            foreach($components as $component) {
                                
                // check if current element has the field
                if(isset($component['id'])) {

                    if($component['id'] == $id) {

                        return $component;

                    }                    

                }

                // if not has, then check if has subcomponents
                if(isset($component['components'])) {
                                                                                                        
                    $found = $this->_getById($component['components'], $id);

                    if($found) {

                        return $found;

                    }
                                                                                                
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
         * getFirstElementByType
         * search in json file for first elementy by type
         * NOTE: normally to get the first form component for validator
         */
        public function getFirstElementByType($type) {

            return $this->_getFirstElementByType($this->_config['components'], $type);
                                                                        
        }

        /**
         * _getFirstElementByType
         * do the magic         
         */
        private function _getFirstElementByType($components, $type) {

            foreach($components as $component) {
                
                // check if current element has the field
                if(isset($component['type'])) {

                    if($component['type'] == $type) {

                        return $component;

                    }                    

                }

                // if not has, then check if has subcomponents
                if(isset($component['components'])) {
                                                                                                        
                    return $this->_getFirstElementByType($component['components'], $type);
                                                                                                
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

         /**
         * setById
         */
        public function setById($id, $arr) { 
            
            $this->_setById($this->_config, $id, $arr);

        }

        /**
         * _setById
         * do the magic recursive
         */
        private function _setById(&$config, $id, $arr) {

            $obj = &$config;

            if(isset($obj['id'])) {

                if($obj['id'] == $id) {

                    $obj = $arr;
                    
                }
                elseif(isset($obj['components'])) {

                    foreach($obj['components'] as $componentKey => $componentValue) {                

                        $this->_setById($obj['components'][$componenKey], $id, $arr);
                        
                    }

                }
                
            }
            else {

                if(isset($obj['components'])) {

                    foreach($obj['components'] as $componenKey => $componentValue) {                

                        $this->_setById($obj['components'][$componenKey], $id, $arr);
                        
                    }
                 
                }
                
            }

        }


        /**
         * getAllelements
         * get all elements in array format
         */
        public function getAllElements($component = null) {

            $return = [];

            if($component != null) {

                $this->_getAllElements($return, $component);

            }
            else {

                $this->_getAllElements($return, $this->_config);

            }

            return $return;

        }

        /**
         * _getAllelements
         * do the magic recursive
         */
        private function _getAllelements(&$return, &$config) {

            $obj = &$config;

            if(isset($obj['components'])) {

                foreach($obj['components'] as $componentKey => $componentValue) {

                    $this->_getAllelements($return, $obj['components'][$componentKey]);
                        
                }
                
            }
            elseif(isset($obj['childrens'])) {

                foreach($obj['childrens'] as $childrenKey => $childrenValue) {

                    $this->_getAllelements($return, $obj['childrens'][$childrenKey]);
                        
                }
                
            }
            else {

                //echo '<br />Add Final Obj To Return';
                //echo '<br />Final Object -> '.print_r($obj, true);
                $return[] = $obj;

            }
                                                                    
        }
        
    }
