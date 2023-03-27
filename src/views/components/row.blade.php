<div class="row {{ $component['class'] }}">    
    <?php
        $cols = 12 / count($component['components']);
    ?>
    <?php foreach($component['components'] as $c) { ?>
        <div class="col-lg-{{ $cols }}">
            <?php
                $jcomponent = new \Softinline\SfwComponent\SfwComponent($controller);
                $jcomponent->setItem(@$item);                
                echo $jcomponent->renderComponent($config, $c, "");
            ?>
        </div>
    <?php } ?>
</div>