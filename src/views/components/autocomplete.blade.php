<?php

    // before render    
    if(array_key_exists('beforeRender', $component)) {
        $method = $component['beforeRender'];
        $component = $controller::$method($component, @$item);
    }

    // value
    $value = "";    
    
    // hidden
    $hidden = "";        
    
    // field
    $field = $component['field'];
    
    // title
    $title = $component['title'];

    // autocompleteUrl
    $autocompleteUrl = $component['autocompleteUrl'];

    // autocompleteFunction
    $autocompleteFunction = $component['autocompleteFunction'];

    // has item, then show value in db
    if(isset($item)) {                
        $value = $controller::$autocompleteFunction($item, @$id);
        $hidden = $item->{$field};
    }
    // no has item, check if we want a default value
    else {        
        if(\Request::get($field) != '') {            
            $value = $controller::$autocompleteFunction(@$item, @$id, \Request::get($field));
            $hidden = \Request::get($field);
        }
    }
    
    // minLength
    $minLength = 2;
    if(isset($component['minLength'])) {
        $minLength = $component['minLength'];
    }

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

    // autocomplete
    $autocomplete = "off";

    // show condition
    $show = true;
    if(array_key_exists('beforeShow', $component)) {
        $method = $component['beforeShow'];
        $show = $controller::$method($component, @$item);
    }

    // get translation file
    $translationFile = 'messages.';
    if(array_key_exists('translationFile', $config)) {
        $translationFile = $config['translationFile'];
    }

    // add help text
    $help = false;
    if(isset($component['help'])) {
        $help = ucfirst(trans($translationFile.$component['help']));
    }
   
?>
<?php if($show) { ?>
    <div class="form-group">
        <label <?php echo $help != '' ? 'title="'.$help.'"' : '';?> <?php echo $help != '' ? 'class="sfwcomponent-help-text"' : '';?> >{{ ucfirst(trans($translationFile.$title)) }}: {{ $required ? '*' : '' }}</label>
        <div style="position:relative;">
            <input type="text" name="{{ $field }}" id="{{ $field }}" class="form-control {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} value="{{ $value }}" sfwcomponent-data-title="{{ ucfirst(trans($translationFile.$title)) }}" autocomplete="{{ $autocomplete }}">
            <span id="{{ $field }}_loading" class="spinner-border spinner-border-sm text-secondary" role="status" aria-hidden="true" style="display:none; position:absolute; right:10px; top:50%; margin-top:-8px; pointer-events:none;"></span>
            <input type="hidden" name="{{ $field }}_autocomplete" id="{{ $field }}_autocomplete" value="{{ $hidden }}"/>
        </div>
        <script>
            $(function() {
                var $input = $("#{{ $field }}");
                var $loading = $("#{{ $field }}_loading");
                $input.autocomplete({
                    source: function( request, response ) {
                        $loading.show();
                        $input.addClass('ui-autocomplete-loading');
                        $.ajax({
                            url: "{{ $autocompleteUrl }}",
                            dataType: "jsonp",
                            data: {
                                term: request.term
                            },
                            success: function(data) {
                                response(data);
                            },
                            complete: function() {
                                $loading.hide();
                                $input.removeClass('ui-autocomplete-loading');
                            }
                        });
                    },
                    minLength: {{ $minLength }},
                    select: function( event, ui ) {
                        $("#{{ $field }}_autocomplete").val(ui.item.id);
                    }
                });
            });
        </script>
    </div>
<?php } ?>