<?php

    // before render    
    if(array_key_exists('beforeRender', $component)) {
        $method = $component['beforeRender'];
        $component = $controller::$method($component, @$item);
    }

    // title
    $title = $component['title'];

    // prepare action link
    $actionType = "link";
    if(array_key_exists('action', $component)) {
        $tmp = explode(':', $component['action']);
        $actionType = $tmp[0];
        $tmp[1] = \Softinline\SfwComponent\SfwUtils::replaceUrlParams($tmp[1]);
    }
       
    // icon
    $icon = "";
    if(isset($component['icon'])) {
        $icon = '<i class="'.$component['icon'].'"></i> ';
    }

    // class
    $class = "";
    if(isset($component['class'])) {
        $class = ' class="'.$component['class'].'" ';
    }

    // disabled    
    $disabled = false;
    if(isset($component['disabled'])) {
        $disabled = $component['disabled'];
    }

    // id
    $id = '';
    if(isset($component['id'])) {
        $id = ' id = "'.$component['id'].'" ';
    }

    // extra
    $extra = '';
    if(isset($component['extra'])) {
        $extra = ' '.$component['extra'].' ';
    }

    // target
    $target = '_self';
    if(isset($component['target'])) {
        $target = $component['target'];
    }

    // show condition
    $show = true;
    if(array_key_exists('beforeShow', $component)) {
        $method = $component['beforeShow'];
        $show = $controller::$method(@$item);
    }

    // type button    
    $buttonType = 'button';
    if(array_key_exists('buttonType', $component)) {        
        $buttonType = $component['buttonType'];
    }

    // get translation file
    $translationFile = 'messages.';
    if(array_key_exists('translationFile', $config)) {
        $translationFile = $config['translationFile'];
    }
    
?>
<?php if($show) { ?>    
    <?php if($actionType == 'js') { ?>
        <button type="button" {!! $class !!} {!! $id !!} {{ $disabled ? 'disabled' : '' }} onclick="{{ $tmp[1] }}" {!! $extra !!} target="{{ $target }}">{!! $icon !!}{{ ucfirst(trans($translationFile.$title)) }}</button>        
    <?php } else { ?>
        <?php
            // add query parameters            
            $queryString =  http_build_query($_GET, '&');
            if($queryString != '') {
                $tmp[1] = $tmp[1].'?'.$queryString;
            }
        ?>
        <?php if($buttonType == "button") { ?>
            <button type="button" {!! $class !!} {!! $id !!} {{ $disabled ? 'disabled' : '' }} onclick="window.open('{{ url($tmp[1]) }}','{{ $target }}')" {!! $extra !!} >{!! $icon !!}{{ ucfirst(trans($translationFile.$title)) }}</button>
        <?php } else { ?>
            <button type="submit" {!! $class !!} {!! $id !!} {{ $disabled ? 'disabled' : '' }} {!! $extra !!} >{!! $icon !!}{{ ucfirst(trans($translationFile.$title)) }}</button>
        <?php } ?>
    <?php } ?>
<?php } ?>