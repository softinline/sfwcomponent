<?php
    $id = "";
    if(isset($component['id'])) {
        $id = ' id="'.$component['id'].'" ';
    }
    $class = "";
    if(isset($component['class'])) {
        $class = ' class="'.$component['class'].'" ';
    }
?>
<h{{ $component['number']}} {!! $class !!} {!! $id !!}>{{ trans('messages.'.$component['text']) }}</h{{ $component['number'] }}>