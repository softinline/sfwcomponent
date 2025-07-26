<?php

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