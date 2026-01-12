<?php

    // action
    $action = \Softinline\SfwComponent\SfwUtils::replaceUrlParams($component['action']);

    // add queryString on url            
    $queryString =  http_build_query($_GET, '&');
    if($queryString != '') {
        $action = $action.'?'.$queryString;
    }

    // id
    $id = $component['id'];

    // class    
    $class = '';
    if(isset($component['class'])) {
        $class = $component['class'];
    }
        
?>
<form class="{{ $class }}" name="{{ $id }}" id="{{ $id }}" method="post" action="{{ url($action) }}" enctype="multipart/form-data">
    <?php echo $content; ?>
</form>