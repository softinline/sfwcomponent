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

    // show condition
    $show = true;
    if(array_key_exists('beforeShow', $component)) {
        $method = $component['beforeShow'];
        $show = $controller::$method(@$item);
    }

?>
<?php if($show) { ?>
    <div class="{{ $class }}" {!! $id !!}>
        <?php echo $content; ?>
    </div>
<?php } ?>