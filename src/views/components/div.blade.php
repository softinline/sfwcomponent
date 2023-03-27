<div class="{{ $component['class'] }}">
    <?php if(array_key_exists('components', $component)) { ?>
        <?php foreach($component['components'] as $c) { ?>
            <?php
                $sfwcomponent = new \Softinline\SfwComponent\SfwComponent($controller);
                $sfwcomponent->setItem(@$item);
                echo $sfwcomponent->renderComponent($config, $c, $content);
            ?>
        <?php } ?>
    <?php } ?>
</div>