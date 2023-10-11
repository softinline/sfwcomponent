<ul class="nav nav-tabs">
    <?php $first = true; ?>
    <?php foreach($component['components'] as $c) { ?>
        <?php
            $show = true;
            if(array_key_exists('beforeShow', $c)) {
                $method = $c['beforeShow'];
                $show = $controller::$method(@$item);
            }
        ?>
        <?php if($show) { ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $first ? 'active' : ''; ?>" href="#tab_{{ $c['key'] }}" data-toggle="tab" aria-expanded="false">{{ ucfirst(trans('messages.'.$c['title'])) }}</a>
            </li>
            <?php $first = false; ?>
        <?php } ?>
    <?php } ?>
</ul>
<div class="tab-content">
    <?php $first = true; ?>    
    <?php foreach($component['components'] as $c) { ?>
        <?php
            $show = true;
            if(array_key_exists('beforeShow', $c)) {
                $method = $c['beforeShow'];
                $show = $controller::$method(@$item);
            }
        ?>
        <?php if($show) { ?>
            <div class="tab-pane <?php echo $first ? 'active' : ''; ?>" id="tab_{{ $c['key'] }}">                
                <?php                                        
                    foreach($c['components'] as $subC) {                                                
                        $jcomponent = new \Softinline\SfwComponent\SfwComponent($controller);
                        $jcomponent->setItem(@$item);                        
                        echo $jcomponent->renderComponents($config, [$subC]);
                    }
                ?>
            </div>
            <?php $first = false; ?>
        <?php } ?>
    <?php } ?>
</div>