<?php 
    $method = $component['selector'];                                                    
    $options = $controller::$method(@$item, @$id);
?>
<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$component['title'])) }}: {{ $component['required'] == 'required' ? '*' : '' }}</label>
    <?php foreach($options['all'] as $optionKey => $optionValue) { ?>
        <br /><input type="checkbox" name="{{ $component['field'] }}[]" id="{{ $component['field'] }}" style="padding:5px" value="{{ $optionKey }}" <?php echo array_key_exists($optionKey, $options['selected']) ? 'checked' : ''; ?> sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$component['title'])) }}"> {{ ucfirst($optionValue) }}
    <?php } ?>
</div>