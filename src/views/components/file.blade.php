<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$component['title'])) }}: {{ $component['required'] ? '*' : '' }}</label>
    <input type="file" name="{{ $component['field'] }}" id="{{ $component['field'] }}" class="{{ $component['required'] ? 'sfwcomponent-frm-item-required' : '' }}" {{ $component['required'] ? 'required' : '' }} value="{{ @$item->{$component['field']} }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$component['title'])) }}">
</div>
<?php if(array_key_exists('show', $component)) { ?>
    <?php
        $method = $component['show'];
        $show = $controller::$method(@$item, @$id);
    ?>
    <?php echo $show; ?>
<?php } ?>