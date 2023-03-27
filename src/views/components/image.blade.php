<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$component['title'])) }}: {{ $component['required'] ? '*' : '' }}</label>
    <input type="file" name="{{ $component['field'] }}" id="{{ $component['field'] }}" class="{{ $component['required'] ? 'sfwcomponent-frm-item-required' : '' }}" {{ $component['required'] ? 'required' : '' }} value="{{ @$item->{$component['field']} }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$component['title'])) }}"  accept="image/*">
    <br />
    <?php if(array_key_exists('show', $component)) { ?>
        <?php
            $method = $component['show'];
            $show = $controller::$method(@$item, @$id);
        ?>
        <?php echo $show; ?>
    <?php } ?>
    <br />
    <canvas id="canvas-{{ $component['field'] }}" style="width:{{ $component['width'] }}px; height:auto;"></canvas>
</div>                
<script>
    $(function() {
        
        var input = document.querySelector('input[id={{ $component['field'] }}]');
        input.onchange = function () {
            var file = input.files[0];
            drawOnCanvas(file);
        };
    
        function drawOnCanvas(file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var dataURL = e.target.result,
                c = document.querySelector("#canvas-{{ $component['field'] }}"),
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