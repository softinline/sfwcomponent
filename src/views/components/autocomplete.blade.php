<?php
    $value = "";
    $hidden = "";
    // has item, then show value in db
    if(isset($item)) {        
        $method = $component['autocompleteFunction'];
        $value = $controller::$method($item, @$id);
        $hidden = $item->{$component['field']};
    }
    // no has item, check if we want a default value
    else {        
        if(\Request::get($component['field']) != '') {
            $method = $component['autocompleteFunction'];            
            $value = $controller::$method(@$item, @$id, \Request::get($component['field']));
            $hidden = \Request::get($component['field']);
        }
    }    
?>
<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$component['title'])) }}: {{ $component['required'] ? '*' : '' }} ({{ trans('messages.start_writing_something') }})</label>
    <input type="text" name="{{ $component['field'] }}" id="{{ $component['field'] }}" class="form-control {{ $component['required'] ? 'sfwcomponent-frm-item-required' : '' }}" {{ $component['required'] ? 'required' : '' }} {{ @$component['disabled'] ? 'disabled' : '' }} value="{{ $value }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$component['title'])) }}">
    <input type="hidden" name="{{ $component['field'] }}_autocomplete" id="{{ $component['field'] }}_autocomplete" value="{{ $hidden }}"/>
    <script>
        $(function() {
            $("#{{ $component['field'] }}").autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "{{ $component['autocompleteUrl'] }}",
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
                minLength: 2,
                select: function( event, ui ) {
                    $("#{{ $component['field'] }}_autocomplete").val(ui.item.id);
                }
            });
        });
    </script>
</div>