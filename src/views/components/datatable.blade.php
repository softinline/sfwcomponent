<?php
    $query = http_build_query(\Request::all());        
    if($query != '') {
        $query = '?'.$query;
    }    
    $query .= $query != '' ? '&' : '?';
    $query .= 'datatable='.$component['name'];    
?>
<table id="table-sfwcomponent-{{ $component['name'] }}" sfwcomponent-data-url="{{ url($component['url']) }}" class="{{ $component['class'] }}" style="width:100%">
    <thead>
        <tr>
            <?php if($component['selector']) { ?>
                <th><input type="checkbox" id="sfwcomponent-chk-select-all" name="sfwcomponent-chk-select-all" class="sfwcomponent-select-all-btn" sfwcomponent-data-datatable="{{ $component['name'] }}" sfwcomponent-data-url="{{ $component['url'] }}" /></th>
            <?php } ?>
            <?php foreach($component['columns'] as $column) { ?>
                <?php         
                    // title                                               
                    $title = $column['field'];
                    if(array_key_exists('title', $column)) {
                        $title = $column['title'];
                    }
                    if($title != '') {
                        $title = ucfirst(trans('messages.'.$title));
                    }
                    // options
                    $options = '';
                    if(array_key_exists('options', $column)) {
                        $options = $column['options'];
                    }
                ?>
                <th <?php echo $options; ?>>{{ $title }}</th>
            <?php } ?>
        </tr>
    </thead>                    
    <tbody>
    </tbody>
    <?php if(@$component['footer']) { ?>
        <tfoot>
            <?php if($component['selector']) { ?>
                <th></th>
            <?php } ?>
            <?php foreach($component['columns'] as $column) { ?>
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
        </tfoot>
    <?php } ?>
</table>
<script>
    $(function() {
    
        if(sfwcomponent.tables["{{ $component['name'] }}"] == undefined || sfwcomponent.tables["{{ $component['name'] }}"] == 'undefined') {
            sfwcomponent.tables["{{ $component['name'] }}"] = Array();
            sfwcomponent.tables["{{ $component['name'] }}"].datatable = null;
            sfwcomponent.tables["{{ $component['name'] }}"].selected = Array();
        }
        
        <?php                
            $data = url($component['url'].'/data/'.$query);            
            $orderCol = '0';
            $orderType = 'desc';
            if(array_key_exists('order', $component)) {
                $orderCol = $component['order']['col'];
                $orderType = $component['order']['type'];
            }

        ?>
        
        sfwcomponent.tables["{{ $component['name'] }}"].datatable = $('#table-sfwcomponent-{{ $component['name'] }}').DataTable({
            "sDom":"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>\n        <'table-responsive'tr>\n        <'row align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>",            
            "stateSave": true,
            "processing": true,
            "serverSide": true,            
            "ajax": "<?php echo $data; ?>",
            "order": [[ {{ $orderCol }}, "{{ $orderType }}" ]],                
            "pageLength": {{ @$component['pageLength'] != '' ? $component['pageLength'] : 10}},
            "columns": [        
                <?php if($component['selector']) { ?>
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
                    ?>
                    { width:"{{ @$column['width'] }}", data:"{{ $column['field'] }}", name:"{{ $column['name'] }}", orderable:{{ $orderable }}, searchable:{{ $searchable }} },
                <?php } ?>
            ],
            "rowCallback": function( row, data ) {                
                <?php if($component['selector']) { ?>
                    var id = data.DT_RowId.split('_');    
                    if ( $.inArray(id[1], sfwcomponent.tables["{{ $component['name'] }}"].selected) !== -1 ) {
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