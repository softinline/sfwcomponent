<?php

    // before render    
    if(array_key_exists('beforeRender', $component)) {
        $method = $component['beforeRender'];
        $component = $controller::$method($component, @$item);
    }

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

    // add help text
    $help = false;
    if(isset($component['help'])) {
        $help = ucfirst(trans($translationFile.$component['help']));
    }

?>
<?php if($show) { ?>
    <div class="form-group">
        <label <?php echo $help != '' ? 'title="'.$help.'"' : '';?> <?php echo $help != '' ? 'class="sfwcomponent-help-text"' : '';?> >{{ ucfirst(trans($translationFile.$title)) }}: {{ $required ? '*' : '' }}</label>
        <div><pre><?php echo print_r($json, true); ?></pre></div>
    </div>
<?php } ?>