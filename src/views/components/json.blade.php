<?php
    $json = json_decode(@$item->{$component['field']}, true);
?>
<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$component['title'])) }}: {{ $component['required'] ? '*' : '' }}</label>
    <div><pre><?php echo print_r($json, true); ?></pre></div>                                                
</div>