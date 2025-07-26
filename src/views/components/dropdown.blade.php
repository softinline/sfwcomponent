<?php

    // title
    $title = $component['title'];

    // class    
    $class = '';
    if(isset($component['class'])) {
        $class = $component['class'];
    }

    // show condition
    $show = true;
    if(array_key_exists('beforeShow', $component)) {
        $method = $component['beforeShow'];
        $show = $controller::$method(@$item);
    }

    // get translation file
    $translationFile = 'messages.';
    if(array_key_exists('translationFile', $config)) {
        $translationFile = $config['translationFile'];
    }

?>
<?php if($show) { ?>
    <div class="btn-group">
        <button type="button" class="{{ $class }}">{{ ucfirst(trans($translationFile.$title)) }}</button>
        <button type="button" class="{{ $class }} dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
            <span class="sr-only">Toggle Dropdown</span>
            <div class="dropdown-menu dropdown-menu-right" role="menu" x-placement="bottom-start">
                <?php foreach($component['options'] as $option) { ?>
                    <?php 
                        // prepare action link
                        $tmp = explode(':', $option['action']);
                    ?>
                    <?php if($tmp[0] == 'js') { ?>
                        <a href="javascript:void(0)" class="dropdown-item" onclick="{{ $tmp[1] }}(this)"><i class="{{ $option['icon'] }}"></i> {{ ucfirst(trans($translationFile.$option['title'])) }}</a>
                    <?php } else { ?>                    
                        <a href="javascript:void(0)" class="dropdown-item" onclick="window.open('{{ url($tmp[1]) }}','_self')"><i class="{{ $option['icon'] }}"></i> {{ ucfirst(trans($translationFile.$option['title'])) }}</a>
                    <?php } ?>
                <?php } ?>
            </div>
        </button>                                
    </div>
<?php } ?>