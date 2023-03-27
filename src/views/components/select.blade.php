<?php 
    $method = $component['selector'];
    $options = $controller::$method(@$item, @$id);
?>
<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$component['title'])) }}: {{ $component['required'] ? '*' : '' }}</label>
    <select name="{{ $component['field'] }}" id="{{ $component['field'] }}" class="form-control {{ $component['required'] ? 'sfwcomponent-frm-item-required' : '' }}" {{ $component['required'] ? 'required' : '' }} {{ @$component['disabled'] ? 'disabled' : '' }} sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$component['title'])) }}">        
        <option value="">{{ ucfirst(trans('messages.select-option')) }}</option>
        <?php foreach($options as $optionKey => $optionValue) { ?>
            <?php
                $selected = '';  
                // determine default value
                // if edit, dont take effect
                // on add check if Request has a param with this value    
                if(@$item) {
                    $selected = $optionKey == @$item->{$component['field']} ? 'selected' : '';
                }
                else {
                    $selected = $optionKey == \Request::get($component['field']) ? 'selected' : '';
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