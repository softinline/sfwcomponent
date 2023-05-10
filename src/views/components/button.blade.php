<?php

    // title
    $title = $component['title'];

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

    // disabled    
    $disabled = false;
    if(isset($component['disabled'])) {
        $disabled = $component['disabled'];
    }

    // id
    $id = '';
    if(isset($component['id'])) {
        $id = ' id = "'.$component['id'].'" ';
    }

?>
<?php if($tmp[0] == 'js') { ?>
    <button type="button" {!! $class !!} {!! $id !!} {{ $disabled ? 'disabled' : '' }} onclick="{{ $tmp[1] }}">{!! $icon !!}{{ ucfirst(trans('messages.'.$title)) }}</button>        
<?php } else { ?>
    <button type="button" {!! $class !!} {!! $id !!} {{ $disabled ? 'disabled' : '' }} onclick="window.open('{{ url($tmp[1]) }}','_self')">{!! $icon !!}{{ ucfirst(trans('messages.'.$title)) }}</button>
<?php } ?>
