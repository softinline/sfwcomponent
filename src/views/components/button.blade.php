<?php

    // title
    $title = $component['title'];

    // prepare action link
    $tmp = explode(':', $component['action']);
    $occurences = \Softinline\SfwComponent\SfwUtils::findAllBetween($tmp[1], '{', '}');                                
    foreach($occurences as $occurence) {        
        if(\Request::route($occurence)) {
            $tmp[1] = str_replace('{'.$occurence.'}', \Request::route($occurence), $tmp[1]);
        }
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

?>
<?php if($show) { ?>
    <?php if($tmp[0] == 'js') { ?>
        <button type="button" {!! $class !!} {!! $id !!} {{ $disabled ? 'disabled' : '' }} onclick="{{ $tmp[1] }}" {!! $extra !!} target="{{ $target }}">{!! $icon !!}{{ ucfirst(trans('messages.'.$title)) }}</button>        
    <?php } else { ?>
        <?php
            // add query parameters            
            $queryString =  http_build_query($_GET, '&');
            if($queryString != '') {
                $tmp[1] = $tmp[1].'?'.$queryString;
            }
        ?>
        <button type="button" {!! $class !!} {!! $id !!} {{ $disabled ? 'disabled' : '' }} onclick="window.open('{{ url($tmp[1]) }}','{{ $target }}')" {!! $extra !!} >{!! $icon !!}{{ ucfirst(trans('messages.'.$title)) }}</button>
    <?php } ?>
<?php } ?>