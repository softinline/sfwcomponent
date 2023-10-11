<?php

    // field
    $field = $component['field'];

    // title
    $title = $component['title'];

    // translate
    $translate = false;
    if(isset($component['translate'])) {
        $translate = true;
    }

    // translateionRequired
    $translationRequired = false;
    if(isset($component['translationRequired'])) {
        $translationRequired = true;
    }

    // translations
    $translations = '';
    if(isset($component['translations'])) {
        $translations['translations'];
    }

    // required
    $required = false;
    if(isset($component['required'])) {
        $required = $component['required'];
    }

    // disabled
    $disabled = false;
    if(isset($component['disabled'])) {
        $disabled = $component['disabled'];
    }

    // class    
    $class = '';
    if(isset($component['class'])) {
        $class = $component['class'];
    }

    // autocomplete
    $autocomplete = "off";
    if(isset($component['autocomplete'])) {
        $autocomplete = $component['autocomplete'];
    }

    // show condition
    $show = true;
    if(array_key_exists('beforeShow', $component)) {
        $method = $component['beforeShow'];
        $show = $controller::$method(@$item);
    }

?>
<?php if($show) { ?>
    <?php if($translate) { ?>
        <div class="form-group">
            <label>{{ ucfirst(trans('messages.'.$title)) }}: {{ $required ? '*' : '' }}</label>
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#tab_{{ $field }}_default" data-toggle="tab" aria-expanded="false" id="li-text-area">
                        Default {{ $required ? '*' : '' }}
                    </a>
                </li>
                @foreach($languages as $language)
                    <li>
                        <a class="nav-link" href="#tab_{{ $field }}_{{ $language->id }}" data-toggle="tab" aria-expanded="false" id="li-text-area">
                            {{ ucfirst($language->language) }} {{ $translationsRequired ? '*' : '' }}
                        </a>
                    </li>
                @endforeach
            </ul>    
            <div class="tab-content">
                <div class="tab-pane active" id="tab_{{ $field }}_default">
                    <div class="form-group">
                        <input type="text" name="{{ $field }}" id="{{ $field }}" class="form-control {{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} value="{{ @$item->{$field} }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$title)) }}" autocomplete="{{ $autocomplete }}">
                    </div>
                </div>
                <?php                                            
                    if($item) {
                        $method = $translations;
                        $translations = $controller::$method($field, @$item, @$item->id);
                    }
                ?>                    
                @foreach($languages as $language)
                    <div class="tab-pane" id="tab_{{ $field }}_{{ $language->id }}">
                        <div class="form-group">                                
                            <input type="text" name="{{ $field }}_{{ $language->id }}" id="{{ $field }}_{{ $language->id }}" class="form-control {{ $class }} {{ $translationsRequired ? 'sfwcomponent-frm-item-required' : '' }}" {{ $translationsRequired ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} value="{{ @$translations[$language->id] }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$title)) }} ({{ $language->id }})" autocomplete="{{ $autocomplete }}">
                        </div>
                    </div>
                @endforeach                    
            </div>     
        </div>
    <?php } else { ?>
        <div class="form-group">
            <label>{{ ucfirst(trans('messages.'.$title)) }}: {{ $required ? '*' : '' }}</label>
            <input type="text" name="{{ $field }}" id="{{ $field }}" class="form-control {{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} value="{{ @$item->{$field} }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$title)) }}" autocomplete="{{ $autocomplete }}">
        </div>
    <?php } ?>
<?php } ?>