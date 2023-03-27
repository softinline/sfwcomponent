<fieldset>
    <legend>{{ $component['title'] }}</legend>
    <?php foreach($component['components'] as $c) { ?>
        <?php
            $jcomponent = new \Softinline\SfwComponent\SfwComponent($controller);
            $jcomponent->setItem(@$item);            
            echo $jcomponent->renderComponent($config, $c, "");
        ?>
    <?php } ?>
</fieldset>