<?php

    // before render    
    if(array_key_exists('beforeRender', $component)) {
        $method = $component['beforeRender'];
        $component = $controller::$method($component, @$item);
    }

    // title
    $title = $component['title'];

    // get translation file
    $translationFile = 'messages.';
    if(array_key_exists('translationFile', $config)) {
        $translationFile = $config['translationFile'];
    }

?>
<fieldset>
    <legend>{{ ucfirst(trans($translationFile.$title)) }}</legend>
    <?php echo $content; ?>
</fieldset>