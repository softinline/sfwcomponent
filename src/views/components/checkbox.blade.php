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

    // show condition
    $show = true;
    if(array_key_exists('beforeShow', $component)) {
        $method = $component['beforeShow'];
        $show = $controller::$method(@$item);
    }

?>
<?php if($show) { ?>
    <div class="form-group">                                                    
        <input type="checkbox" name="{{ $field }}" id="{{ $field }}" class="{{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }} value="1" <?php echo @$item->{$field} === 1 ? 'checked' : ''; ?> sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$title)) }}">
        <label>{{ ucfirst(trans('messages.'.$title)) }} {{ $required ? '*' : '' }}</label>
    </div>
    @include('sfwcomponent::conditional-components', [
        'component' => $component,
        'item' => @$item,
    ])
<?php } ?>