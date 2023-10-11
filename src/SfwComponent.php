<?php

    namespace Softinline\SfwComponent;
    
    use Illuminate\Http\Request;

    class SfwComponent
    {
        
        private $_controller;
        private $_id;
        private $_item;

        /**
         * constructor
         * @param $id, is the base id for all requests
         * urls, etc...
         */
        public function __construct($controller=null) {
            
            $this->_controller = $controller;

        }
        
        /**
         * return the controller
         * @return string
         */
        public function getController() {

            return $this->_controller;

        }

        /**
         * sets the controller
         * @return void
         */
        public function setController($controller) {

            $this->_controller = $controller;

        }

        /**
         * return the id
         * @return string
         */
        public function getId() {

            return $this->_id;

        }

        /**
         * sets the id
         * @return void
         */
        public function setId($id) {

            $this->_id = $id;

        }

         /**
         * return the id
         * @return string
         */
        public function getItem() {

            return $this->_item;

        }

        /**
         * sets the id
         * @return void
         */
        public function setItem($item) {

            $this->_item = $item;

        }

        /**
         * load model relations
         * @return void
         */        
        public function loadRelations($relations) {

            foreach($relations as $relation) {
                
                $this->_item->load($relation);
                
            }

            $arr = \Arr::dot($this->_item->toArray());

            $this->_item = $arr;

        }

        /**
         * render the main component
         */
        public function render($config) {

            // get components childrens
            $components = $config['components'];
            
            // render components using tree recursive
            $content = self::renderComponents($config, $components);

            // return
            $return = View('sfwcomponent::wrapper', [                
                'content' => $content,
                'config' => $config,
            ])->render();

            return $return;

        }

        /**
         * renderComponents
         * render components getting all sub components if needed
         */
        public function renderComponents($config, $components) {

            $content = '';
            $return = '';

            foreach($components as $component) {
                                                                                                                                
                // if has sub elements render all childrens
                if(array_key_exists('components', $component)) {
                                                        
                    foreach($component['components'] as $c) {
                                                
                        $content .= self::renderComponents($config, [$c]);

                    }

                    $return .= self::renderComponent($config, $component, $content);
                    
                }
                else {
                    
                    $return .= self::renderComponent($config, $component, $content);

                }
                                                                            
            }
                                    
            return $return;

        }

        /**
         * render individual component
         */
        public function renderComponent($config, $component, $content) {

            // set languages
            $languages = [];
            if(array_key_exists('translate', $component)) {
                if($component['translate']) {
                    $languages= false;
                    if(array_key_exists('languages', $component)) {
                        $method = $component['languages'];
                        $languages = $this->_controller::$method(@$item);
                    }                
                }
            }
                                   
            if($component['type'] == 'function') {

                $method = $component['function'];
                $return = $this->_controller::$method($this->_item);

            }
            elseif($component['type'] == 'screen') {

                // nothing
                $return = "";

            }
            else {
                                
                // default file
                $componentFile = 'sfwcomponent::components.'.$component['type'];

                if(view()->exists('sfw.components.'.$component['type'])) {                    
                    $componentFile = 'sfw.components.'.$component['type'];
                }

                // if component is a view use view instead of this
                if($component['type'] == 'view') {

                    $componentFile = $component['view'];

                }

                // return render
                $return = View($componentFile, [
                    'component' => $component,
                    'content' => $content,
                    'controller' => $this->_controller,
                    'item' => $this->_item,
                    'id' => $this->_id,
                    'config' => $config,
                    "languages" => []//$languages,
                ])->render();

            }
                                                                        
            return $return;
            
        }

        /**
         * execute validations
         * process the form, 
         * and return the response
         * @return operation
         */
        public function submit($config, $method, $formId = null, $tabKey = null) {
            
            $cConfig = $config->getConfig();

            // check if its ajax
            $ajax = '';
            if(array_key_exists('ajax', $cConfig)) {
                $ajax = $cConfig['ajax'] ? '#' : '';
            }
            
            // default redirects on ok or ko
            $redirectBack = false;
            $cConfig['url'] = $ajax.$cConfig['url'];
            $redirectOk = $cConfig['url'];
            $redirectKo = $cConfig['url'];

            // check if form has a redirect param
            $formComponent = null;            
            if($formId != null) {
                $formComponent = $config->getById($formId);
                if($formComponent) {                    
                    if(isset($formComponent['redirect'])) {
                        $redirectOk = $formComponent['redirect'];
                        $redirectKo = $formComponent['redirect'];
                        if($formComponent['redirect'] == 'back') {
                            $redirectBack = true;
                        }
                    }
                }                
            }

            // replace dynamic {id} params on final redirectOk or redirectKo
            $occurences = \Softinline\SfwComponent\SfwUtils::findAllBetween($redirectOk, '{', '}');                                
            foreach($occurences as $occurence) {        
                if(\Request::route($occurence)) {
                    $redirectOk = str_replace('{'.$occurence.'}', \Request::route($occurence), $redirectOk);
                    $redirectKo = str_replace('{'.$occurence.'}', \Request::route($occurence), $redirectKo);
                }
            }

            // create or update message
            $msg = is_null($this->_id) ? 'created' : 'updated';

            // check Validators
            $validator = $this->validate($config, $formId, $tabKey);

            if(!$validator->fails()) {
                
                \DB::beginTransaction();

                try {

                    $result = $this->_controller::$method($this->_item);
                                        
                    // default response info
                    $successStatus = $result;
                    $successMessageOk = ucfirst(trans('sfw.'.$msg.'_ok'));
                    $successMessageKo = ucfirst(trans('sfw.'.$msg.'_error'));

                    // is response is array override with other data
                    if(is_array($result)) {                        

                        $successStatus = $result['success'];
                        $successMessageOk = $result['message'];
                        $successMessageKo = $result['message'];

                        // restore redirect Url
                        $redirectOk = $cConfig['url'];
                        $redirectKo = $cConfig['url'];

                    }
                    else {

                        if($result) {
                            $redirectOk = str_replace('{id}', $result->id, $redirectOk);
                            $redirectKo = str_replace('{id}', $result->id, $redirectKo);
                        }

                    }
                                        
                    // operation its ok
                    if($successStatus) {

                        \DB::commit();

                        // makes the redirects
                        if($cConfig['ajax']) {

                            return \Response::json([
                                'success' => true,
                                'message' => $successMessageOk,
                                'type' => 'redirect',
                                'redirect' => $redirectOk,
                            ], 200);

                        }
                        else {

                            \Session::flash('message_success', $successMessageOk);

                            if($redirectBack) {

                                return \Redirect::back();

                            }
                            else {  

                                return \Redirect::to($redirectOk);

                            }

                        }

                    }
                    else {

                        \DB::rollback();

                        if($cConfig['ajax']) {

                            return \Response::json([
                                'success' => false,
                                'message' => $successMessageKo,
                            ], 200);

                        }
                        else {
                            
                            \Session::flash('message_error', $successMessageKo);
                                                                            
                            if($redirectBack) {

                                return \Redirect::back()
                                    ->withInput();

                            }
                            else {

                                return \Redirect::to($redirectKo)
                                    ->withInput();

                            }
                            
                        }

                    }

                }
                catch(\Exception $e) {
                    
                    \DB::rollback();
                    
                    \Log::error('Error Message '.$e->getMessage());
                    \Log::error('Error Trace '.$e->getTraceAsString());

                    if($cConfig['ajax']) {

                        return \Response::json([
                            'success' => false,
                            'message' => ucfirst(trans('sfw.'.$msg.'_error')),
                        ], 200);

                    }
                    else {

                        \Session::flash('message_error', ucfirst(trans('sfw.'.$msg.'_error')));
                        
                        if($redirectBack) {
                            
                            return \Redirect::back()
                                ->withInput();
                                
                        }
                        else  {

                            return \Redirect::to($redirectKo)
                                ->withInput();

                        }

                    }

                    
                }

            }
            else {

                if($cConfig['ajax']) {

                    return \Response::json([
                        'success' => false,
                        'message' => $validator->errors()->first(),
                    ], 200);

                }
                else {

                    return \Redirect::to($redirectKo)
                        ->withInput()
                        ->with('message_error', $validator->errors()->first());

                }

            }
            
            /*
                if($successStatus) {
                    // after execute method check if redirectOk must be changed
                    if(array_key_exists('optionsPostSave', $config['forms'][$form])) {
                        if(\Request::get('optionsPostSave') != '') {
                            $redirectOk = $ajax.str_replace('{id}', $result->id, $config['forms'][$form]['optionsPostSave'][\Request::get('optionsPostSave')][3]);
                        }
                    }
                }
            */
            
        }

        /**
         * perform the validation
         * rules
         */
        private function validate($config, $formId, $tabKey) {

            if($formId != null) {

                if($tabKey != null) {
                    
                    $compoment = $config->getByTypeAndField('tab', $tabKey);

                }
                else {

                    $component = $config->getById($formId);

                }

            }
            else {
                
                $component = $config->getFirstElementByType('form');

            }
            
            $validatorRules = [];

            $allFields = $config->getAllElements($component);

            foreach($allFields as $allField) {

                $validatorRule = '';
                if(isset($allField['required']) && $allField['required']) {
                    $validatorRule .= 'required';
                }
                if(isset($allField['validator'])) {
                    $validatorRule .= $validatorRule != '' ? '|' : '';
                    $validatorRule .= $allField['validator'];
                    $validatorRules[$allField['field']] = $validatorRule;
                }
                
            }

            // make validator
            $validator = \Validator::make(\Request::all(), $validatorRules);

            return $validator;

        }

        /**
         * toggle enable
         */
        public function toggleEnable($config, $method) {
                
            // check if its ajax
            $ajax = '';
            if(array_key_exists('ajax', $config)) {
                $ajax = $config['ajax'] ? '#' : '';
            }
            
            // replace dynamic {id} with id
            $config['url'] = $ajax.str_replace('{id}', $this->_id, $config['url']);

            // if has item call to method
            if($this->_item) {

                // call method in class child
                $response = $this->_controller::$method($this->_item);

                // if the response isnt array
                if(!is_array($response)) {

                    if($response) {
                                                                        
                        return \Response::json([
                            'success' => true,
                            'message' => ucfirst(trans('sfw.updated_ok')),
                            'type' => 'redirect',
                            'redirect' => $config['url'],
                        ], 200);

                    }
                    else {
                        
                        return \Response::json([
                            'success' => false,
                            'message' => ucfirst(trans('sfw.updated_error')),
                        ], 200);
                        
                    }

                }
                else {

                    if($response['success']) {
                    
                        return \Response::json([
                            'success' => true,
                            'message' => $response['message'],
                            'type' => 'redirect',
                            'redirect' => $config['url'],
                        ], 200);

                    }
                    else {

                        return \Response::json([
                            'success' => false,
                            'message' => $response['message'],
                        ], 200);

                    }

                }

            }
            else {

                return \Response::json([
                    'success' => false,
                    'message' => ucfirst(trans('messages.item_not_found')),
                ], 200);

            }

        }

        /**
         * encapsulate export method
         * using Spreadsheet package
         * @return string with data
         */
        public function export($config, $method) {
                        
            // increase vars
            ini_set('max_input_vars', 3000);
            ini_set('suhosin.get.max_vars', 3000);
            ini_set('suhosin.post.max_vars', 3000);
            ini_set('suhosin.request.max_vars', 3000);
            set_time_limit(0);

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();            
            $sheet = $spreadsheet->getActiveSheet();

            // call method in class child
            $sheet = $this->_controller::$method($sheet);

            // create writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            // prepare for download
            header("Content-Type:application/vnd.ms-excel; charset=utf-8");
            header("Content-type:application/x-msexcel; charset=utf-8");
            header('Content-Disposition: attachment; filename="file.xlsx"');
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            ob_start();
            $writer->save("php://output");
            $xlsData = ob_get_contents();
            ob_end_clean();

            // send to browser
            return \Response::json([
                'success' => true,
                'data'=>"data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
            ]);        

        }

        /**
         * delete the item
         */
        public function delete($config, $method) {

            // ajax request
            $ajax = '';
            if(array_key_exists('ajax', $config)) {
                $ajax = $config['ajax'] ? '#' : '';
            }

            // replace dynamic {id} with id
            $config['url'] = $ajax.str_replace('{id}', $this->_id, $config['url']);

            // if has item
            if($this->_item) {

                try {

                    // call to method
                    $result = $this->_controller::$method($this->_item);

                    if($result) {

                        \DB::commit();

                        if(\Request::ajax()) {

                            return \Response::json([
                                'success' => true,
                                'message' => ucfirst(trans('sfw.deleted_ok')),
                                'type' => 'redirect',
                                'redirect' => $config['url'],
                            ], 200);

                        }
                        else {

                            \Session::flash('flash_message', ucfirst(trans('sfw.deleted_ok')));

                            return \Redirect::to($config['url']);

                        }
                    }
                    else {
                        
                        if(\Request::ajax()) {

                            return \Response::json([
                                'success' => false,
                                'message' => ucfirst(trans('sfw.deleted_error')),
                            ], 200);

                        }
                        else {

                            return \Redirect::to($config['url'])
                                ->withInput()
                                ->with('message', ucfirst(trans('sfw.deleted_error')));

                        }
                    }
                }
                // catch error
                catch(\Exception $e) {

                    \DB::rollback();

                    \Log::error('Error Message '.$e->getMessage());
                    \Log::error('Error Trace '.$e->getTraceAsString());

                    if(\Request::ajax()) {

                        return \Response::json([
                            'success' => false,
                            'message' => ucfirst(trans('sfw.deleted_error')),
                        ], 200);

                    }
                    else {

                        return \Redirect::to($redirectKo)
                            ->withInput()
                            ->with('message', ucfirst(trans('sfw.deleted_error')));
                    }

                }

            }
            // no item found
            else {

                if(\Request::ajax()) {

                    return \Response::json([
                        'success' => false,
                        'message' => ucfirst(trans('messages.item_not_found')),
                    ], 200);

                }
                else {

                    return \Redirect::to($config['url'])
                        ->withInput()
                        ->with('message', ucfirst(trans('messages.item_not_found')));

                }

            }

        }
                                    
    }
