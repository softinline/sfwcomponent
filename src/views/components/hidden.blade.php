<?php

    // before render    
    if(array_key_exists('beforeRender', $component)) {
        $method = $component['beforeRender'];
        $component = $controller::$method($component, @$item);
    }

    // field
    $field = $component['field'];

    // value
    $value = $component['value'];

?>
<input type="hidden" name="{{ $field }}" id="{{ $field }}"  value="{{ $value }}">