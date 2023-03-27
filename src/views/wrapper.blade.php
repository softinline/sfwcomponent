<?php   
    // wrapper extends
    $layout = $config['layout'];
?>
@extends($layout, [
])
@section('content')
    <?php echo $content; ?>
@stop