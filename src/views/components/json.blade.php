<?php

    // title
    $title = $component['title'];

    // json
    $json = json_decode(@$item->{$component['field']}, true);

?>
<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$title)) }}: </label>
    <div><pre><?php echo print_r($json, true); ?></pre></div>
</div>