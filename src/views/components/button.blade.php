<?php
    // prepare action link
    $tmp = explode(':', $component['action']);
?>
<?php if($tmp[0] == 'js') { ?>
    <button type="button" class="{{ $component['class'] }}" onclick="{{ $tmp[1] }}">{{ ucfirst(trans('messages.'.$component['title'])) }}</button>        
<?php } else { ?>
    <button type="button" class="{{ $component['class'] }}" onclick="window.open('{{ url($tmp[1]) }}','_self')">{{ ucfirst(trans('messages.'.$component['title'])) }}</button>
<?php } ?>
