<?php

    // prepare action link
    $tmp = explode(':', $component['action']);

    // icon
    $icon = "";
    if(isset($component['icon'])) {
        $icon = '<i class="'.$component['icon'].'"></i> ';
    }

    // class
    $class = "";
    if(isset($component['class'])) {
        $class = ' class="'.$component['class'].'" ';
    }
?>
<?php if($tmp[0] == 'js') { ?>
    <button type="button" {!! $class !!} {!! $id !!} onclick="{{ $tmp[1] }}">{!! $icon !!}{{ ucfirst(trans('messages.'.$component['title'])) }}</button>        
<?php } else { ?>
    <button type="button" {!! $class !!} {!! $id !!} onclick="window.open('{{ url($tmp[1]) }}','_self')">{!! $icon !!}{{ ucfirst(trans('messages.'.$component['title'])) }}</button>
<?php } ?>
