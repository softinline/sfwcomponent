<?php

    // title
    $title = $component['title'];

    // json
    $json = json_decode(@$item->{$component['field']}, true);

    // show condition
    $show = true;
    if(array_key_exists('beforeShow', $component)) {
        $method = $component['beforeShow'];
        $show = $controller::$method(@$item);
    }

?>
<?php if($show) { ?>
    <div class="form-group">
        <label>{{ ucfirst(trans('messages.'.$title)) }}: </label>
        <div><pre><?php echo print_r($json, true); ?></pre></div>
    </div>
<?php } ?>