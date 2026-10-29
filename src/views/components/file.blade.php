<?php

    // before render    
    if(array_key_exists('beforeRender', $component)) {
        $method = $component['beforeRender'];
        $component = $controller::$method($component, @$item);
    }

    // title
    $title = $component['title'];

    // field
    $field = $component['field'];

    // get translation file
    $translationFile = 'messages.';
    if(array_key_exists('translationFile', $config)) {
        $translationFile = $config['translationFile'];
    }

    // add help text
    $help = false;
    if(isset($component['help'])) {
        $help = ucfirst(trans($translationFile.$component['help']));
    }

    // required
    $required = false;
    if(isset($component['required'])) {
        $required = $component['required'];
    }

    // visible
    $visible = true;
    if(isset($component['visible'])) {
        $visible = $component['visible'];
    }

    // showLavel
    $showLabel = true;
    if(isset($component['showLabel'])) {
        $showLabel = $component['showLabel'];
    }

    // name input
    $name = $field;
    if(isset($component['name'])) {
        $name = $component['name'];
    }

?>
<div class="form-group">
    <?php if($showLabel) { ?>
        <label <?php echo $help != '' ? 'title="'.$help.'"' : '';?> <?php echo $help != '' ? 'class="sfwcomponent-help-text"' : '';?> >{{ ucfirst(trans($translationFile.$title)) }}: {{ $required ? '*' : '' }}</label>
    <?php } ?>
    <?php if(array_key_exists('show', $component)) { ?>
        <?php
            $method = $component['show'];
            $show = $controller::$method(@$item, @$id);
        ?>
        <?php echo $show; ?>
    <?php } ?>    
    <input type="file" style="{{ (!$visible ? 'display:none' : '') }}" name="{{ $name }}" id="{{ $field }}" class="{{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} value="{{ @$item->{$field} }}" sfwcomponent-data-title="{{ ucfirst(trans($translationFile.$title)) }}">
</div>