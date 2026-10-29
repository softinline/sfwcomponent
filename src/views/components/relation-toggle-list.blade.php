<?php
    // method
    $method = isset($component['selector']) ? $component['selector'] : null;
    $items = [];
    if ($method && method_exists($controller, $method)) {
        $items = $controller::$method($item);
    } elseif ($method && is_callable([$controller, $method])) {
        $items = call_user_func([$controller, $method], $item);
    }

    // name
    $name = isset($component['name']) ? $component['name'] : 'relationList';
    
    // togglewOnCheck
    $toggleOnCheck = isset($component['toggleOnCheck']) ? $component['toggleOnCheck'] : true;

    // tableClass
    $tableClass = isset($component['tableClass']) ? $component['tableClass'] : 'table table-bordered table-striped';
?>
<div class="sfwcomponent-relation-toggle-list mt-3">    
    <table class="sfwcomponent-relation-toggle-table {{ $tableClass }}">
        <thead>
            <tr>
                <th style="width:60px;">{{ ucfirst(trans('messages.enable')) }}</th>
                <?php foreach($component['cols'] as $col) { ?>
                    <th>{{ ucfirst(trans('messages.'.$col['title'])) }}</th>
                <?php } ?>
                <?php foreach($component['components'] as $c) { ?>
                    <th>{{ ucfirst(trans('messages.'.$c['title'])) }}</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $row) { ?>
                <?php
                    $rowId = isset($row['id']) ? $row['id'] : null;
                    if ($rowId === null) continue;
                    $active = isset($row['active']) && $row['active'] ? true : false;
                    $rowName = isset($row['name']) ? $row['name'] : (isset($row['title']) ? $row['title'] : $rowId);
                ?>                    
                <tr class="sfwcomponent-relation-toggle-row">
                    <td class="text-center">
                        <input type="checkbox"
                                class="sfwcomponent-relation-toggle-check"
                                name="{{ $name }}[{{ $rowId }}][active]"
                                value="1"
                                {{ $active ? 'checked' : '' }}>
                    </td>                    
                    <?php foreach($component['cols'] as $col) { ?>
                        <td>{{ ucfirst($row['cols'][$col['field']]) }}</td>
                    <?php } ?>                    
                    <?php foreach($component['components'] as $c) { ?>
                        <td>
                            <?php 
                                // override some settings
                                $c['showLabel'] = false;
                                $c['name'] = $name.'['.$rowId.']['.$c['field'].']';
                                $c['rowId'] = $rowId;
                                $c['value'] = $row['pivot'][$c['field']];
                                $c['class'] = "sfwcomponent-relation-toggle-extra";
                                $c['visible'] = $active ? true : false;
                            ?>
                            <?php
                                $sfwcomponent = new \Softinline\SfwComponent\SfwComponent($controller);
                                $sfwcomponent->setItem($item);
                                echo $sfwcomponent->renderComponents($config, [$c], "");
                            ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>