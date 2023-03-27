<?php if(@$component['translate']) { ?>
    <div class="form-group">
        <label>{{ ucfirst(trans('messages.'.$component['title'])) }}: {{ $component['required'] ? '*' : '' }}</label>
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="#tab_{{ $component['field'] }}_default" data-toggle="tab" aria-expanded="false" id="li-text-area">                
                    Default {{ $component['required'] ? '*' : '' }}
                </a>
            </li>
            @foreach($languages as $language)
                <li>
                    <a class="nav-link" href="#tab_{{ $component['field'] }}_{{ $language->id }}" data-toggle="tab" aria-expanded="false" id="li-text-area">
                        {{ ucfirst($language->language) }} {{ $component['translationsRequired'] ? '*' : '' }}
                    </a>
                </li>
            @endforeach
        </ul>    
        <div class="tab-content">
            <div class="tab-pane active" id="tab_{{ $component['field'] }}_default">
                <div class="form-group">
                    <input type="text" name="{{ $component['field'] }}" id="{{ $component['field'] }}" class="form-control {{ $component['required'] ? 'sfwcomponent-frm-item-required' : '' }}" {{ $component['required'] ? 'required' : '' }} {{ @$component['disabled'] ? 'disabled' : '' }} value="{{ @$item->{$component['field']} }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$component['title'])) }}">
                </div>
            </div>
            <?php                                            
                if($item) {
                    $method = $component['translations'];
                    $translations = $controller::$method($component['field'], @$item, @$item->id);
                }
            ?>                    
            @foreach($languages as $language)
                <div class="tab-pane" id="tab_{{ $component['field'] }}_{{ $language->id }}">
                    <div class="form-group">                                
                        <input type="text" name="{{ $component['field'] }}_{{ $language->id }}" id="{{ $component['field'] }}_{{ $language->id }}" class="form-control {{ $component['translationsRequired'] ? 'sfwcomponent-frm-item-required' : '' }}" {{ $component['translationsRequired'] ? 'required' : '' }} {{ @$component['disabled'] ? 'disabled' : '' }} value="{{ @$translations[$language->id] }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$component['title'])) }} ({{ $language->id }})">
                    </div>
                </div>
            @endforeach                    
        </div>     
    </div>
<?php } else { ?>
    <div class="form-group">
        <label>{{ ucfirst(trans('messages.'.$component['title'])) }}: {{ $component['required'] ? '*' : '' }}</label>
        <input type="text" name="{{ $component['field'] }}" id="{{ $component['field'] }}" class="form-control {{ $component['required'] ? 'sfwcomponent-frm-item-required' : '' }}" {{ $component['required'] ? 'required' : '' }} {{ @$component['disabled'] ? 'disabled' : '' }} value="{{ @$item->{$component['field']} }}" sfwcomponent-data-title="{{ ucfirst(trans('messages.'.$component['title'])) }}">
    </div>
<?php } ?>