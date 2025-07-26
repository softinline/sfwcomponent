<?php

    // number
    $number = $component['number'];

    // text
    $text = $component['text'];

    // id
    $id = "";
    if(isset($component['id'])) {
        $id = ' id="'.$component['id'].'" ';
    }

    // class
    $class = "";
    if(isset($component['class'])) {
        $class = ' class="'.$component['class'].'" ';
    }

    // get translation file
    $translationFile = 'messages.';
    if(array_key_exists('translationFile', $config)) {
        $translationFile = $config['translationFile'];
    }

?>
<h{{ $number }} {!! $class !!} {!! $id !!}>{{ trans($translationFile.$text) }}</h{{ $number }}>