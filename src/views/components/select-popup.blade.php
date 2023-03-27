<?php 
    $method = $component['selector'];            
    $options = $controller::$method(@$item, @$id);
?>
<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$component['title'])) }}: {{ $component['required'] ? '*' : '' }}</label>
    <select name="{{ $component['field'] }}" id="{{ $component['field'] }}" class="form-control {{ $component['required'] ? 'sfwcomponent-frm-item-required' : '' }}" {{ $component['required'] ? 'required' : '' }} sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$component['title'])) }}">
        <option value="">{{ ucfirst(trans('messages.select-option')) }}</option>
        <?php foreach($options as $optionKey => $optionValue) { ?>
            <option value="{{ $optionKey }}" <?php echo $optionKey ==  @$item->{$component['field']} ? 'selected' : ''; ?>>{{ ucfirst($optionValue) }}</option>
        <?php } ?>
    </select>
    <button type="button" class="btn btn-primary {{ @$config['btnStyles'] }} mt-2" onclick="$('#modal-{{ $component['field'] }}').modal()">{{ ucfirst(trans('messages.options')) }}</button>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal-{{ $component['field'] }}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ ucfirst(trans('messages.select-option')) }} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">                        
                <?php foreach($options as $optionKey => $optionValue) { ?>                            
                    <div class="row sfwcomponent-selectable-row <?php echo $optionKey ==  @$item->{$component['field']} ? 'sfwcomponent-selectable-row-selected' : ''; ?>" id="sfwcomponent-selectable-row-{{ $optionKey }}" onclick="sfwcomponent.selectPopUpOption('{{ $component['field'] }}', '{{ $optionKey }}'); modals.close('#modal-{{ $component['field'] }}')">
                        <div class="col-lg-12">
                            {{ ucfirst($optionValue) }}
                            <br /><small>{{ $optionKey }}</small>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="modal-footer">                        
                <button type="button" class="btn btn-secondary {{ @$config['btnStyles'] }}" data-dismiss="modal">{{ ucfirst(trans('messages.accept')) }}</button>
            </div>
        </div>
    </div>
</div>