<?php
    $action = $component['action'];

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
<form class="{{ $component['class'] }}" id="{{ $component['id'] }}" method="post" action="{{ url($action) }}">
    <?php echo $content; ?>
</form>