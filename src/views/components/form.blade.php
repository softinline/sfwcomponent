<?php

    $action = $component['action'];

    $id = $component['id'];

    // class    
    $class = '';
    if(isset($component['class'])) {
        $class = $component['class'];
    }

    // check if its ajax    
    $ajax = '';
    /*
    if(array_key_exists('ajax', $cConfig)) {
        $ajax = $cConfig['ajax'] ? '#' : '';
    }
    */

    // replace dynamic {id} with id
    $action = $ajax.str_replace('{id}', @$item->id, $action);
?>
<form class="{{ $class }}" id="{{ $id }}" method="post" action="{{ url($action) }}">
    <?php echo $content; ?>
</form>