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

?>
<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$title)) }}: {{ $required ? '*' : '' }}</label>
    <input type="password" name="{{ $field }}" id="{{ $field }}" class="form-control {{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$title)) }}">
</div>