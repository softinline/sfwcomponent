<?php

    // field
    $field = $component['field'];

    // title
    $title = $component['title'];

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

    // format
    $format = "d/m/Y";
    if(isset($component['format'])) {
        $format = $component['format'];
    }

    // autocomplete
    $autocomplete = "off";

    // class    
    $class = '';
    if(isset($component['class'])) {
        $class = $component['class'];
    }

    // placeholder
    $placeholder = '';
    if(isset($component['placeholder'])) {
        $placeholder = $component['placeholder'];
    }

    // show condition
    $show = true;
    if(array_key_exists('beforeShow', $component)) {
        $method = $component['beforeShow'];
        $show = $controller::$method(@$item);
    }

    // get translation file
    $translationFile = 'messages.';
    if(array_key_exists('translationFile', $config)) {
        $translationFile = $config['translationFile'];
    }    
    
?>
<?php if($show) { ?>
    <div class="form-group">
        <label>{{ ucfirst(trans($translationFile.$title)) }}: {{ $required ? '*' : '' }}</label>
        <input type="text" name="{{ $field }}" id="{{ $field }}" class="form-control time-picker {{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} value="{{ @$item->{$field} != '' ? $item->{$field}->format($format) : '' }}" autocomplete="{{ $autocomplete }}" sfwcomponent-data-title="{{ ucfirst(trans($translationFile.$title)) }}" placeholder="{{ $placeholder }}">
    </div>
<?php } ?>