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

    // rows
    $rows = 5;
    if(isset($component['rows'])) {
        $rows = $component['rows'];
    }

?>
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
                    <textarea name="{{ $field }}" id="{{ $field }}" class="form-control {{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} rows="{{ $rows }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$title)) }}">{{ @$item->{$field} }}</textarea>
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
                        <textarea name="{{ $field }}_{{ $language->id }}" id="{{ $field }}_{{ $language->id }}" class="form-control {{ $class }} {{ $translationsRequired ? 'sfwcomponent-frm-item-required' : '' }}" {{ $translationsRequired ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} rows="{{ $rows }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$title)) }}  ({{ $language->id }})">{{ @$translations[$language->id] }}</textarea>
                    </div>
                </div>
            @endforeach                    
        </div>     
    </div>
<?php } else { ?>
    <div class="form-group">
        <label>{{ ucfirst(trans('messages.'.$title)) }}: {{ $required ? '*' : '' }}</label>
        <textarea name="{{ $field }}" id="{{ $field }}" class="form-control {{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} rows="{{ $rows }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$title)) }}">{{ @$item->{$field} }}</textarea>
    </div>
<?php } ?>