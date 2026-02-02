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

?>
<?php if($show) { ?>
    <div class="form-group">                                                    
        <input type="checkbox" name="{{ $field }}" id="{{ $field }}" class="{{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }} value="1" <?php echo @$item->{$field} === 1 ? 'checked' : ''; ?> sfwcomponent-data-title="{{ ucfirst(trans($translationFile.$title)) }}">
        <label for="{{ $field }}" <?php echo $help != '' ? 'title="'.$help.'"' : '';?> <?php echo $help != '' ? 'class="sfwcomponent-help-text"' : '';?> >{{ ucfirst(trans($translationFile.$title)) }}: {{ $required ? '*' : '' }}</label>
    </div>
    @include('sfwcomponent::conditional-components', [
        'component' => $component,
        'item' => @$item,
    ])
<?php } ?>