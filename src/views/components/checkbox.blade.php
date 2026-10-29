<?php

    // before render    
    if(array_key_exists('beforeRender', $component)) {
        $method = $component['beforeRender'];
        $component = $controller::$method($component, @$item);
    }

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

    // show condition
    $show = true;
    if(array_key_exists('beforeShow', $component)) {
        $method = $component['beforeShow'];
        $show = $controller::$method(@$item);
    }

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

    // name input
    $name = $field;
    if(isset($component['name'])) {
        $name = $component['name'];
    }

    // value
    $value = @$item->{$field};
    if(isset($component['value'])) {
        $value = $component['value'];
    }

?>
<?php if($show) { ?>
    <div class="form-group">                                                    
        <input type="checkbox" style="{{ (!$visible ? 'display:none' : '') }}" name="{{ $name }}" id="{{ $field }}" class="{{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }} value="1" <?php echo $value === 1 ? 'checked' : ''; ?> sfwcomponent-data-title="{{ ucfirst(trans($translationFile.$title)) }}">
        <?php if($showLabel) { ?>
            <label for="{{ $field }}" <?php echo $help != '' ? 'title="'.$help.'"' : '';?> <?php echo $help != '' ? 'class="sfwcomponent-help-text"' : '';?> >{{ ucfirst(trans($translationFile.$title)) }} {{ $required ? '*' : '' }}</label>
        <?php } ?>
    </div>
    @include('sfwcomponent::conditional-components', [
        'component' => $component,
        'item' => @$item,
    ])
<?php } ?>