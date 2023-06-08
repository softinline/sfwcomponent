<?php

    // action
    $action = $component['action'];
    $occurences = \Softinline\SfwComponent\SfwUtils::findAllBetween($action, '{', '}');                                
    foreach($occurences as $occurence) {        
        if(\Request::route($occurence)) {
            $action = str_replace('{'.$occurence.'}', \Request::route($occurence), $action);
        }
    }

    // id
    $id = $component['id'];

    // class    
    $class = '';
    if(isset($component['class'])) {
        $class = $component['class'];
    }
        
?>
<form class="{{ $class }}" name="{{ $id }}" id="{{ $id }}" method="post" action="{{ url($action) }}">
    <?php echo $content; ?>
</form>