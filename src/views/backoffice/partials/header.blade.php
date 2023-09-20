<?php   
    $sfwObjects = \Softinline\SfwComponent\Models\SfwObject::select()
        ->where('type', '=', 1)
        ->where('system', '=', 1)
        ->whereNull('sfw_object_id')
        ->orderBy('order')
        ->get();
?>
<?php foreach($sfwObjects as $sfwObject) { ?>
    <a href="{{ url($sfwObject->url) }}">{{ $sfwObject->object }}</a>
<?php } ?>