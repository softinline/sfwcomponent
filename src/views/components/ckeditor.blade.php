<?php

    // before render    
    if(array_key_exists('beforeRender', $component)) {
        $method = $component['beforeRender'];
        $component = $controller::$method($component, @$item);
    }

    // field
    $field = $component['field'];

    // title
    $title = $component['title'];

    // translate
    $translate = false;
    if(isset($component['translate'])) {
        $translate = $component['translate'];
    }

    // translateionRequired
    $translationRequired = false;
    if(isset($component['translationRequired'])) {
        $translationRequired = $component['translationRequired'];
    }

    // translations
    $translationsMethod = '';
    if(isset($component['translations'])) {
        $translationsMethod = $component['translations'];
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

    // rows
    $rows = 5;
    if(isset($component['rows'])) {
        $rows = $component['rows'];
    }

    // collapsed
    $collapsed = false;
    if(isset($component['collapsed'])) {
        $collapsed = $component['collapsed'];
    }
    $collapseId = 'sfwcomponent-collapsible-'.$field;

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
    <?php if($translate) { ?>
        <div class="form-group">
            <label <?php echo $help != '' ? 'title="'.$help.'"' : '';?> <?php echo $help != '' ? 'class="sfwcomponent-help-text"' : '';?> <?php if($collapsed) { ?> style="cursor:pointer;" onclick="var e=document.getElementById('{{ $collapseId }}'); if(e.style.display==='none'){e.style.display='block'; if(typeof window['sfwcomponentInitCkeditor_{{ $field }}']==='function'){window['sfwcomponentInitCkeditor_{{ $field }}']();}}else{e.style.display='none';}" <?php } ?>>{{ ucfirst(trans($translationFile.$title)) }}: {{ $required ? '*' : '' }}<?php if($collapsed) { ?> <small>(...)</small><?php } ?></label>
            <div id="{{ $collapseId }}" style="<?php echo $collapsed ? 'display:none;' : ''; ?>">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#tab_{{ $field }}_default" data-toggle="tab" aria-expanded="false" id="li-text-area">
                        Default {{ $required ? '*' : '' }}
                    </a>
                </li>
                @foreach($languages as $language)
                    <li>
                        <a class="nav-link" href="#tab_{{ $field }}_{{ $language->id }}" data-toggle="tab" aria-expanded="false" id="li-text-area">
                            {{ ucfirst($language->language) }} {{ $translationRequired ? '*' : '' }}
                        </a>
                    </li>
                @endforeach
            </ul>    
            <div class="tab-content">
                <div class="tab-pane active" id="tab_{{ $field }}_default">
                    <div class="form-group">                            
                        <textarea name="{{ $field }}" id="{{ $field }}" class="form-control {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} rows="{{ $rows }}" sfwcomponent-data-title="{{ ucfirst(trans($translationFile.$title)) }}">{{ @$item->{$field} }}</textarea>
                    </div>
                </div>
                <?php
                    $translations = [];
                    if($item) {
                        $translations = $controller::$translationsMethod($item, $field);
                    }
                ?>
                @foreach($languages as $language)
                    <div class="tab-pane" id="tab_{{ $field }}_{{ $language->id }}">
                        <div class="form-group">                                
                            <textarea name="{{ $field }}_{{ $language->id }}" id="{{ $field }}_{{ $language->id }}" class="form-control {{ $translationRequired ? 'sfwcomponent-frm-item-required' : '' }}" {{ $translationRequired ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} rows="{{ $rows }}" sfwcomponent-data-title="{{ ucfirst(trans($translationFile.$title)) }} ({{ $language->id }})">{{ @$translations[$language->id] }}</textarea>
                        </div>
                    </div>
                @endforeach                    
            </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="form-group">
            <label <?php echo $help != '' ? 'title="'.$help.'"' : '';?> <?php echo $help != '' ? 'class="sfwcomponent-help-text"' : '';?> <?php if($collapsed) { ?> style="cursor:pointer;" onclick="var e=document.getElementById('{{ $collapseId }}'); if(e.style.display==='none'){e.style.display='block'; if(typeof window['sfwcomponentInitCkeditor_{{ $field }}']==='function'){window['sfwcomponentInitCkeditor_{{ $field }}']();}}else{e.style.display='none';}" <?php } ?>>{{ ucfirst(trans($translationFile.$title)) }}: {{ $required ? '*' : '' }}<?php if($collapsed) { ?> <small>(...)</small><?php } ?></label>
            <div id="{{ $collapseId }}" style="<?php echo $collapsed ? 'display:none;' : ''; ?>">
                <textarea name="{{ $field }}" id="{{ $field }}" class="form-control {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} rows="{{ $rows }}" sfwcomponent-data-title="{{ ucfirst(trans($translationFile.$title)) }}">{{ @$item->{$field} }}</textarea>
            </div>
        </div>
    <?php } ?>
    <script>
        $(document).ready(function() {
            window['sfwcomponentInitCkeditor_{{ $field }}'] = function() {
                if (window['sfwcomponentCkeditorInited_{{ $field }}']) {
                    return;
                }
                if (!window.CKEDITOR) {
                    alert('CKEditor not found!');
                    return;
                }
                var options = {
                    skin: 'kama',
                    enterMode: CKEDITOR.ENTER_BR,
                    shiftEnterMode: CKEDITOR.ENTER_BR,
                    coreStyles_bold: {
                        element: 'b',
                        overrides: 'strong'
                    }
                };
                <?php if($translate) { ?>
                    if (document.getElementById('{{ $field }}')) { CKEDITOR.replace('{{ $field }}', options); }
                    <?php foreach($languages as $language) { ?>
                        if (document.getElementById('{{ $field }}_{{ $language->id }}')) { CKEDITOR.replace('{{ $field }}_{{ $language->id }}', options); }
                    <?php } ?>
                <?php } else { ?>
                    if (document.getElementById('{{ $field }}')) { CKEDITOR.replace('{{ $field }}', options); }
                <?php } ?>
                window['sfwcomponentCkeditorInited_{{ $field }}'] = true;
            };
            <?php if(!$collapsed) { ?>
                window['sfwcomponentInitCkeditor_{{ $field }}']();
            <?php } ?>
        });
    </script>
<?php } ?>