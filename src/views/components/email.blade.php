<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$component['title'])) }}: {{ $component['required'] ? '*' : '' }}</label>                                
    <input type="email" name="{{ $component['field'] }}" id="{{ $component['field'] }}" class="form-control {{ $component['required'] ? 'sfwcomponent-frm-item-required' : '' }}" {{ $component['required'] ? 'required' : '' }} {{ @$component['disabled'] ? 'disabled' : '' }} value="{{ @$item->{$component['field']} }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$component['title'])) }}">
</div>