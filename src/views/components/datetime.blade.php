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
    
?>
<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$title)) }}: {{ $required ? '*' : '' }}</label>
    <input type="text" name="{{ $field }}" id="{{ $field }}" data-toggle="datetimepicker" class="form-control datetime-picker {{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} value="{{ @$item->{$field} != '' ? $item->{$field}->format($format) : '' }}" autocomplete="{{ $autocomplete }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$title)) }}">
</div>