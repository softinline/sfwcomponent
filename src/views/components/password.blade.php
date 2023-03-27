<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$component['title'])) }}: {{ $component['required'] ? '*' : '' }}</label>
    <input type="password" name="{{ $component['field'] }}" id="{{ $component['field'] }}" class="form-control {{ $component['required'] ? 'sfwcomponent-frm-item-required' : '' }}" {{ $component['required'] ? 'required' : '' }} sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$component['title'])) }}">
</div>