<?php 
    $method = $component['selector'];                                                    
    $options = $controller::$method(@$item, @$id);
?>
<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$component['title'])) }}: {{ $component['required'] ? '*' : '' }}</label>
    <select name="{{ $component['field'] }}[]" id="{{ $component['field'] }}" class="form-control {{ $component['required'] ? 'sfwcomponent-frm-item-required' : '' }}" {{ $component['required'] ? 'required' : '' }} multiple="true" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$component['title'])) }}">
        <?php foreach($options['all'] as $optionKey => $optionValue) { ?>
            <option style="padding:5px" value="{{ $optionKey }}" <?php echo array_key_exists($optionKey, $options['selected']) ? 'selected' : ''; ?>>{{ ucfirst($optionValue) }}</option>
        <?php } ?>
    </select>
</div>