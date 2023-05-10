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

?>
<h{{ $number }} {!! $class !!} {!! $id !!}>{{ trans('messages.'.$text) }}</h{{ $number }}>