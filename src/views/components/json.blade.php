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

    // get translation file
    $translationFile = 'messages.';
    if(array_key_exists('translationFile', $config)) {
        $translationFile = $config['translationFile'];
    }

?>
<?php if($show) { ?>
    <div class="form-group">
        <label>{{ ucfirst(trans($translationFile.$title)) }}: </label>
        <div><pre><?php echo print_r($json, true); ?></pre></div>
    </div>
<?php } ?>