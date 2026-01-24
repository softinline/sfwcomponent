<?php

    // title
    $title = $component['title'];

    // get translation file
    $translationFile = 'messages.';
    if(array_key_exists('translationFile', $config)) {
        $translationFile = $config['translationFile'];
    }

    // add help text
    $help = false;
    if(isset($component['help'])) {
        $help = ucfirst($component['help']);
    }

?>
<div class="form-group">
    <label <?php echo $help != '' ? 'title="'.$help.'"' : '';?> <?php echo $help != '' ? 'class="sfwcomponent-help-text"' : '';?> >{{ ucfirst(trans($translationFile.$title)) }}: {{ $required ? '*' : '' }}</label>
    <input type="file" name="{{ $component['field'] }}" id="{{ $component['field'] }}" class="{{ $component['required'] ? 'sfwcomponent-frm-item-required' : '' }}" {{ $component['required'] ? 'required' : '' }} value="{{ @$item->{$component['field']} }}" sfwcomponent-data-title="{{ ucfirst(trans($translationFile.$title)) }}">
</div>
<?php if(array_key_exists('show', $component)) { ?>
    <?php
        $method = $component['show'];
        $show = $controller::$method(@$item, @$id);
    ?>
    <?php echo $show; ?>
<?php } ?>