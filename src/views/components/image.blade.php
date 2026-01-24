<?php

    // field
    $field = $component['field'];

    // title
    $title = $component['title'];

    // get translation file
    $translationFile = 'messages.';
    if(array_key_exists('translationFile', $config)) {
        $translationFile = $config['translationFile'];
    }

    // add help text
    $help = false;
    if(isset($component['help'])) {
        $help = ucfirst(trans('messages.').$component['help']);
    }

?>

<div class="form-group">
    <label <?php echo $help != '' ? 'title="'.$help.'"' : '';?> <?php echo $help != '' ? 'class="sfwcomponent-help-text"' : '';?> >{{ ucfirst(trans($translationFile.$title)) }}: {{ $required ? '*' : '' }}</label>
    <input type="file" name="{{ $field }}" id="{{ $field }}" class="{{ $component['required'] ? 'sfwcomponent-frm-item-required' : '' }}" {{ $component['required'] ? 'required' : '' }} value="{{ @$item->{$field} }}" sfwcomponent-data-title="{{ ucfirst(trans($translationFile.$title)) }}"  accept="image/*">
    <br />
    <?php if(array_key_exists('show', $component)) { ?>
        <?php
            $method = $component['show'];
            $show = $controller::$method(@$item, @$id);
        ?>
        <?php echo $show; ?>
    <?php } ?>
    <br />
    <canvas id="canvas-{{ $field }}" style="width:{{ $component['width'] }}px; height:auto;"></canvas>
</div>                
<script>
    $(function() {
        
        var input = document.querySelector('input[id={{ $field }}]');
        input.onchange = function () {
            var file = input.files[0];
            drawOnCanvas(file);
        };
    
        function drawOnCanvas(file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var dataURL = e.target.result,
                c = document.querySelector("#canvas-{{ $field }}"),
                ctx = c.getContext('2d'),
                img = new Image();

                img.onload = function() {                        
                    c.width = img.width;
                    c.height = img.height;
                    ctx.drawImage(img, 0, 0);
                };

                img.src = dataURL;
            };
            reader.readAsDataURL(file);
        }

    });
</script>