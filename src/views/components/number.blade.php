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

    // autocomplete
    $autocomplete = "off";

    // class    
    $class = '';
    if(isset($component['class'])) {
        $class = $component['class'];
    }

    // step
    $step = '';
    if(isset($component['step'])) {
        $step = $component['step'];
    }

    // show condition
    $show = true;
    if(array_key_exists('beforeShow', $component)) {
        $method = $component['beforeShow'];
        $show = $controller::$method(@$item);
    }

?>
<?php if($show) { ?>
    <div class="form-group">
        <label>{{ ucfirst(trans('messages.'.$title)) }}: {{ $required ? '*' : '' }}</label>
        <input type="number" step="{{ $step }}" name="{{ $field }}" id="{{ $field }}" class="form-control {{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} value="{{ @$item->{$field} }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$title)) }}">
    </div>
<?php } ?>