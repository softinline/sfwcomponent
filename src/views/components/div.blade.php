<?php

    // class    
    $class = '';
    if(isset($component['class'])) {
        $class = $component['class'];
    }

    // id
    $id = '';
    if(isset($component['id'])) {
        $id = ' id = "'.$component['id'].'" ';
    }

?>
<div class="{{ $class }}" {!! $id !!}>
    <?php echo $content; ?>
</div>