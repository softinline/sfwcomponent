<?php

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
    $autocomplete = "off"

?>
<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$title)) }}: {{ $required ? '*' : '' }} ({{ trans('messages.start_writing_something') }})</label>
    <input type="text" name="{{ $field }}" id="{{ $field }}" class="form-control {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} value="{{ $value }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$title)) }}" autocomplete="{{ $autocomplete }}">
    <input type="hidden" name="{{ $field }}_autocomplete" id="{{ $field }}_autocomplete" value="{{ $hidden }}"/>
    <script>
        $(function() {
            $("#{{ $field }}").autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "{{ $autocompleteUrl }}",
                        dataType: "jsonp",
                        data: {
                            term:request.term                                
                        },
                        success: function(data) {
                            response(data);
                        },
                        complete: function() {                                
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