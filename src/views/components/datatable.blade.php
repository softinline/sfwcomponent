<?php

    // query
    $query = http_build_query(\Request::all());        
    if($query != '') {
        $query = '?'.$query;
    }    
    $query .= $query != '' ? '&' : '?';
    $query .= 'datatable='.$component['name'];    

    // name
    $name = $component['name'];

    // url    
    $url = \Softinline\SfwComponent\SfwUtils::replaceUrlParams($component['url']);
        
    // selector
    $selector = $component['selector'];

    // class    
    $class = "";
    if(isset($component['class'])) {
        $class = $component['class'];
    }

    // footer
    $footer = false;
    if(isset($component['footer'])) {
        $footer = true;
    }

    // show condition
    $show = true;
    if(array_key_exists('beforeShow', $component)) {
        $method = $component['beforeShow'];
        $show = $controller::$method(@$item);
    }

    // config cols
    $configCols = false;
    if(array_key_exists('configCols', $component)) {
        $configCols = $component['configCols'];
    }

    if($configCols) {

        // get config in database for config cols
        if(\Auth::user()) {

            $datatableConfigCols = \Softinline\SfwComponent\Models\SfwDatatable::select()
                ->where('datatable', '=', $name)
                ->where('user_id', '=', \Auth::user()->id)
                ->first();

        }
        else {

            $datatableConfigCols = \Softinline\SfwComponent\Models\SfwDatatable::select()
                ->where('datatable', '=', $name)            
                ->first();

        }

        // if found one config, then override
        if($datatableConfigCols) {

            $component['columns'] = json_decode($datatableConfigCols->config, true);

        }

    }

    // get translation file
    $translationFile = 'messages.';
    if(array_key_exists('translationFile', $config)) {
        $translationFile = $config['translationFile'];
    }

?>
<?php if($show) { ?>
    <?php if($configCols) { ?>
        <div class="row">
            <div class="col-lg-12 text-right text-end mb-2">
                <button class="btn btn-primary" onclick="$('#sfwcomponent-datatable-config-columns-{{ $name }}').modal('show')"><i class="las la-cog"></i></button>
            </div>
        </div>
        <div class="modal fade show" tabindex="-1" id="sfwcomponent-datatable-config-columns-{{ $name }}" aria-modal="true" role="dialog">
            <div class="modal-dialog  modal-lg ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Configurar Columnas</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>                        
                    </div>
                    <div class="modal-body">
                        <form id="sfwcomponent-form-datatable-config-columns-{{ $name }}" name="sfwcomponent-form-datatable-config-columns-{{ $name }}">                            
                            <ul id="sortable">                                                            
                                <?php foreach($component['columns'] as $column) { ?>
                                    <?php
                                        // title                                               
                                        $title = $column['field'];
                                        if(array_key_exists('title', $column)) {
                                            $title = $column['title'];
                                        }
                                        if($title != '') {
                                            $title = ucfirst(trans($translationFile.$title));
                                        }

                                        // defult false is not show
                                        $default = true;
                                        if(array_key_exists('default', $column)) {
                                            $default = $column['default'];
                                        }
                                    ?>
                                    <li class="ui-state-default" id="{{ $column['field'] }}">
                                        <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                        <input type="checkbox" id="check-{{ $column['field'] }}" name="check-{{ $column['field'] }}" value="1" <?php echo $default ? 'checked' : ''; ?>> {{ $title }}
                                    </li>                                                                        
                                    <input type="hidden" name="json-{{ $column['field'] }}" id="json-{{ $column['field'] }}" value="<?php echo htmlspecialchars(json_encode($column), ENT_QUOTES, 'UTF-8'); ?>" />
                                <?php } ?>
                            </ul>                            
                            <input type="hidden" name="name" id="name" value="{{ $name }}" />
                            <input type="hidden" name="file" id="file" value="{{ $name }}" />
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="sfwcomponent.datatableConfigColumnsSave('sfwcomponent-form-datatable-config-columns-{{ $name }}',1)">Guardar</button>
                        <button type="button" class="btn btn-primary" onclick="sfwcomponent.datatableConfigColumnsSave('sfwcomponent-form-datatable-config-columns-{{ $name }}',2)">Guardar para Todos</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $( "#sortable" ).sortable();
        </script>
    <?php } ?>    
    <table id="table-sfwcomponent-{{ $name }}" sfwcomponent-data-url="{{ url($url) }}" class="{{ $class }}" style="width:100%">
        <thead>
            <tr>
                <?php if($selector) { ?>
                    <th><input type="checkbox" id="sfwcomponent-chk-select-all" name="sfwcomponent-chk-select-all" class="sfwcomponent-select-all-btn" sfwcomponent-data-datatable="{{ $name }}" sfwcomponent-data-url="{{ url($url) }}" /></th>
                <?php } ?>
                <?php foreach($component['columns'] as $column) { ?>
                    <?php         
                        // title                                               
                        $title = $column['field'];
                        if(array_key_exists('title', $column)) {
                            $title = $column['title'];
                        }
                        if($title != '') {
                            $title = ucfirst(trans($translationFile.$title));
                        }
                        // options
                        $options = '';
                        if(array_key_exists('options', $column)) {
                            $options = $column['options'];
                        }
                        // className used for dt-right, dt-body-right, etc... NOTE: must be defined this in your CSS
                        $className = '';
                        if(@$column['className'] != '') {
                            $className = $column['className'];
                        }
                        // defult false is not show
                        $default = true;
                        if(array_key_exists('default', $column)) {
                            $default = $column['default'];
                        }
                    ?>
                    <?php if($default) { ?>
                        <th <?php echo $options; ?>>{{ $title }}</th>
                    <?php } ?>
                <?php } ?>
            </tr>
        </thead>                    
        <tbody>
        </tbody>
        <?php if($footer) { ?>
            <tfoot>
                <?php if($selector) { ?>
                    <th></th>
                <?php } ?>
                <?php foreach($component['columns'] as $column) { ?>
                    <?php
                        // defult false is not show
                        $default = true;
                        if(array_key_exists('default', $column)) {
                            $default = $column['default'];
                        }
                    ?>
                    <?php if($default) { ?>
                        <?php if(array_key_exists('searchable', $column)) { ?>
                            <?php if($column['searchable']) { ?>
                                <th sfwcomponent-data-searchable="true"></th>
                            <?php } else { ?>
                                <th></th>
                            <?php } ?>
                        <?php } else { ?>
                            <th sfwcomponent-data-searchable="true"></th>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </tfoot>
        <?php } ?>
    </table>
    <script>
        $(function() {
        
            if(sfwcomponent.tables["{{ $name }}"] == undefined || sfwcomponent.tables["{{ $name }}"] == 'undefined') {
                sfwcomponent.tables["{{ $name }}"] = Array();
                sfwcomponent.tables["{{ $name }}"].datatable = null;
                sfwcomponent.tables["{{ $name }}"].selected = Array();
            }
            
            <?php                
                $data = url($url.'/data/'.$query);            
                $orderCol = '0';
                $orderType = 'desc';
                if(array_key_exists('order', $component)) {
                    $orderCol = $component['order']['col'];
                    $orderType = $component['order']['type'];
                }

            ?>
            
            sfwcomponent.tables["{{ $name }}"].datatable = $('#table-sfwcomponent-{{ $name }}').DataTable({
                "sDom":"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>\n        <'table-responsive'tr>\n        <'row align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>",            
                "stateSave": true,
                "processing": true,
                "serverSide": true,            
                "ajax": "<?php echo $data; ?>",
                "order": [[ {{ $orderCol }}, "{{ $orderType }}" ]],                
                "pageLength": {{ @$component['pageLength'] != '' ? $component['pageLength'] : 10}},
                "columns": [        
                    <?php if($selector) { ?>
                        { width:"1%", data:"selector", name:"selector", orderable:false, searchable:false },
                    <?php } ?>                
                    <?php foreach($component['columns'] as $column) { ?>
                        <?php
                            $orderable = 'true';
                            if(@$column['orderable']===false) {
                                $orderable = 'false';
                            }
                            $searchable = 'true';
                            if(@$column['searchable']===false) {
                                $searchable = 'false';
                            }
                            // defult false is not show
                            $default = true;
                            if(array_key_exists('default', $column)) {
                                $default = $column['default'];
                            }
                        ?>
                        <?php if($default) { ?>
                            { width:"{{ @$column['width'] }}", data:"{{ $column['field'] }}", name:"{{ $column['name'] }}", orderable:{{ $orderable }}, searchable:{{ $searchable }} className:'{{ $className }}'},
                        <?php } ?>
                    <?php } ?>
                ],
                "rowCallback": function( row, data ) {                
                    <?php if($selector) { ?>
                        var id = data.DT_RowId.split('_');    
                        if ( $.inArray(id[1], sfwcomponent.tables["{{ $name }}"].selected) !== -1 ) {
                            $(row).find('.sfwcomponent-selector').prop('checked', true);
                        }
                    <?php } ?>
                    @if(array_key_exists('rowCallBack', $component))
                        @include($component['rowCallBack'], [
                            'config' => $config,
                        ])
                    @endif                
                },
                "drawCallBack": function(settings, json) {                
                    @if(array_key_exists('drawCallBack', $component))
                        @include($component['drawCallBack'], [
                            'config' => $config,
                        ]);
                    @endif                
                },            
                @if(array_key_exists('extra', $component))
                    @include($component['extra'])
                @endif            
            });
            
        });
    </script>

<?php } ?>