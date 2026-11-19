<?php

    // before render
    if (array_key_exists('beforeRender', $component)) {
        $method = $component['beforeRender'];
        $component = $controller::$method($component, @$item);
    }

    $field = $component['field'];
    $name = array_key_exists('name', $component) ? $component['name'] : $field;
    $value = array_key_exists('value', $component) ? $component['value'] : ($item[$field] ?? '');

?>
<input type="hidden" name="{{ $name }}" id="{{ $field }}" value="{{ $value }}">