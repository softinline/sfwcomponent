<?php 

    // options
    $method = $component['selector'];
    $options = $controller::$method(@$item, @$id);

    // field
    $field = $component['field'];

    // title
    $title = $component['title'];

    // required
    $required = false;
    if(isset($component['required'])) {
        $required = $component['required'];
    }

    // disabled
    $disabled = false;
    if(isset($component['disabled'])) {
        $disabled = $component['disabled'];
    }

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

?>
<?php if($show) { ?>
    <div class="form-group">
        <label>{{ ucfirst(trans('messages.'.$title)) }}: {{ $required ? '*' : '' }}</label>
        <select name="{{ $field }}" id="{{ $field }}" class="form-control sfwcomponent-select2 {{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$title)) }}">
            <option value="">{{ ucfirst(trans('messages.select-option')) }}</option>

            <?php foreach($options as $option) { ?>
            <?php if($option['title']) { ?> <optgroup label="{{$option['title']}}"> <?php } ?>
                <?php foreach($option['options'] as $optionKey => $optionValue) { ?>
                    <?php
                        $selected = '';  
                        // determine default value
                        // if edit, dont take effect
                        // on add check if Request has a param with this value    
                        if(@$item) {
                            $selected = $optionKey == @$item->{$field} ? 'selected' : '';
                        }
                        else {
                            $selected = $optionKey == \Request::get($field) ? 'selected' : '';
                        }
                    ?>
                    <option value="{{ $optionKey }}" {{ $selected }}>{{ ucfirst($optionValue) }}</option>
                <?php } ?>
                
            <?php if($option['title']) { ?> </optgroup> <?php } ?>
            <?php } ?>

        </select>
    </div>
    <script>
        $(document).ready(function() {
            $('.sfwcomponent-select2').select2();
        });
    </script>
<?php } ?>