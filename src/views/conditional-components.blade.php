<?php if(array_key_exists('childrens', $component)) { ?>
    <?php foreach($component['childrens'] as $children) { ?>
        <script>
            $(function() {
                $("#{{ $component['field'] }}").on('change', function() {                
                    <?php if($component['type'] == 'checkbox') { ?>
                        var value = $('input[name="{{ $component['field'] }}"]:checked').length > 0;
                    <?php } else { ?>
                        var value = $("#{{ $component['field'] }}").val();
                    <?php } ?>                
                    $("#div-{{ $component['field'] }}-{{ $children['value'] }}").hide();
                    if(value == '{{ $children['value'] }}') {
                        $("#div-{{ $component['field'] }}-{{ $children['value'] }}").toggle('slow');
                    }
                });
            });
        </script>
        <?php
            // check if display
            $display = @$item->{$component['field']} == $children['value'] ? 'block' : 'none'; 
        ?>
        <div id="div-{{ $component['field'] }}-{{ $children['value'] }}" style="display:{{ $display }}" class="sfwcomponent-childrens-div">
            <?php foreach($children['components'] as $c) { ?>
                <?php
                    $sfwcomponent = new \Softinline\SfwComponent\SfwComponent($controller);
                    $sfwcomponent->setItem($item);
                    echo $sfwcomponent->renderComponents($config, [$c], "");
                ?>
            <?php } ?>
        </div>
    <?php } ?>
<?php } ?>