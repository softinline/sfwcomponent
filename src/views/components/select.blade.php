<?php 
    
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

    // options
    $method = $component['selector'];
    $options = $controller::$method(@$item, @$id, @$args);

    // linkedToObject
    $linkedToObject = false;
    $linkedToUrl = false;

    if(array_key_exists('linkedToObject', $component)) {

        $linkedToObject = $component['linkedToObject'];
        $linkedToUrl = $component['linkedToUrl'];

    }

    // get translation file
    $translationFile = 'messages.';
    if(array_key_exists('translationFile', $config)) {
        $translationFile = $config['translationFile'];
    }

    // add help text
    $help = false;
    if(isset($component['help'])) {
        $help = ucfirst($component['help']);
    }
    
?>
<?php if($show) { ?>    
    <div class="form-group">
        <label <?php echo $help != '' ? 'title="'.$help.'"' : '';?> <?php echo $help != '' ? 'class="sfwcomponent-help-text"' : '';?> >{{ ucfirst(trans($translationFile.$title)) }}: {{ $required ? '*' : '' }}</label>
        <select name="{{ $field }}" id="{{ $field }}" class="form-control {{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} sfwcomponent-data-title="{{ ucfirst(trans($translationFile.$title)) }}">
            <option value="">{{ ucfirst(trans($translationFile.'select-option')) }}</option>            
            <?php foreach($options as $optionKey => $optionValue) { ?>                
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
        </select>
    </div>
    @include('sfwcomponent::conditional-components', [
        'component' => $component,
        'item' => @$item,
    ])
    <?php if($linkedToObject) { ?>
        <script>
            $("#{{ $linkedToObject }}").on('change', function() {
                var select = $('#{{ $field }}');
                $.ajax({
                    url: "{{ $linkedToUrl }}",
                    method:"get",                  
                    data: {
                        term:$("#{{ $linkedToObject }}").val()
                    },
                    success: function(data) {
                        var htmlOptions = [];
                        for( item in data ) {
                            html = '<option value="' + data[item].id + '">' + data[item].description + '</option>';
                            htmlOptions[htmlOptions.length] = html;
                        }
                        // here you will empty the pre-existing data from you selectbox and will append the htmlOption created in the loop result
                        select.empty().append( htmlOptions.join('') );
                    },
                    complete: function() {
                    }
                });
            })
        </script>
    <?php } ?>
<?php } ?>