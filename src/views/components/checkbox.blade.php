<?php

    // field
    $field = $component['field'];

    // title
    $title = $component['title'];

    // class
    $class = "";
    if(isset($component['class'])) {
        $class = $component['class'];
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
    
?>
<div class="form-group">                                                    
    <input type="checkbox" name="{{ $field }}" id="{{ $field }}" class="{{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }} value="1" <?php echo @$item->{$field} === 1 ? 'checked' : ''; ?> sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$title)) }}">
    <label>{{ ucfirst(trans('messages.'.$title)) }} {{ $required ? '*' : '' }}</label>
</div>