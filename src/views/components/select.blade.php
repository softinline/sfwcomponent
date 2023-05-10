<?php 

    // options
    $method = $component['selector'];
    $options = $controller::$method(@$item, @$id);

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

    // class    
    $class = '';
    if(isset($component['class'])) {
        $class = $component['class'];
    }

?>
<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$title)) }}: {{ $required ? '*' : '' }}</label>
    <select name="{{ $field }}" id="{{ $field }}" class="form-control {{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$title)) }}">
        <option value="">{{ ucfirst(trans('messages.select-option')) }}</option>
        <?php foreach($options as $optionKey => $optionValue) { ?>
            <?php
                $selected = '';  
                // determine default value
                // if edit, dont take effect
                // on add check if Request has a param with this value    
                if(@$item) {
                    $selected = $optionKey == @$item->{$field} ? 'selected' : '';
                }
                else {
                    $selected = $optionKey == \Request::get($field) ? 'selected' : '';
                }
            ?>
            <option value="{{ $optionKey }}" {{ $selected }}>{{ ucfirst($optionValue) }}</option>
        <?php } ?>
    </select>
</div>
@include('sfwcomponent::conditional-components', [
    'component' => $component,
    'item' => @$item,
])