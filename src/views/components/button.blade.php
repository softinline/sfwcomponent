<?php
    // prepare action link
    $tmp = explode(':', $component['action']);
    $icon = "";
    if(isset($component['icon'])) {
        $icon = '<i class="'.$component['icon'].'"></i> ';
    }
?>
<?php if($tmp[0] == 'js') { ?>
    <button type="button" class="{{ $component['class'] }}" onclick="{{ $tmp[1] }}">{!! $icon !!}{{ ucfirst(trans('messages.'.$component['title'])) }}</button>        
<?php } else { ?>
    <button type="button" class="{{ $component['class'] }}" onclick="window.open('{{ url($tmp[1]) }}','_self')">{!! $icon !!}{{ ucfirst(trans('messages.'.$component['title'])) }}</button>
<?php } ?>
