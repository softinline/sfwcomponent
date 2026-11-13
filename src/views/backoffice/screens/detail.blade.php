<link rel="stylesheet" href="https://cdn.softinline.com/plugins/jquery-sortable-lists/jquery-sortable-lists.css">
<script src="https://cdn.softinline.com/plugins/jquery-sortable-lists/jquery-sortable-lists.min.js"></script>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <?php echo $tree; ?>
            </div>
        </div>
    </div>
</div>
<script>
    (function() {
        function initSortableTree() {
            if (typeof $ === 'undefined' || !$.fn.sortableLists || !$('#tree').length) return;
            if ($('#tree').data('sfwSortableInit')) return;
            var options = {
                placeholderCss: {'background-color': '#ff8'},
                hintCss: {'background-color':'#bbf'},
                opener: {
                    active: true,
                    as: 'html',
                    close: '<i class="fa fa-minus c3"></i>',
                    open: '<i class="fa fa-plus"></i>',
                    openerCss: {
                        'display': 'inline-block',
                        'float': 'left',
                        'margin-left': '-35px',
                        'margin-right': '5px',
                        'font-size': '1.1em'
                    }
                },
                ignoreClass: 'clickable'
            };
            $('#tree').sortableLists(options);
            $('#tree').data('sfwSortableInit', true);
        }

        function hierarchyFromList($ul) {
            var out = [];
            $ul.children('li').each(function() {
                var $li = $(this);
                var id = $li.attr('data-id') || $li.attr('id');
                if (!id) return;
                var node = {id: String(id)};
                var $childUl = $li.children('ul').first();
                if ($childUl.length) {
                    node.children = hierarchyFromList($childUl);
                }
                out.push(node);
            });
            return out;
        }

        function getTreeHierarchy() {
            var $tree = $('#tree');
            if (!$tree.length) return null;
            if ($.fn.sortableListsToHierarchy) {
                try { return $tree.sortableListsToHierarchy(); } catch (e) {}
            }
            if ($.fn.sortableListsToArray) {
                try {
                    var arr = $tree.sortableListsToArray();
                    if (Array.isArray(arr)) { return arr; }
                } catch (e) {}
            }
            return hierarchyFromList($tree);
        }

        $(function() { initSortableTree(); });
        setTimeout(initSortableTree, 300);
        setTimeout(initSortableTree, 1000);

        window.sfwSaveScreenTree = function(screenId) {
            if (!$('#tree').length) {
                alert('Tree not ready');
                return;
            }
            var hierarchy = getTreeHierarchy();
            if (!hierarchy) {
                alert('Tree not ready');
                return;
            }
            $.ajax({
                url: '{{ url("sfw/screens") }}/' + screenId + '/order',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    tree: JSON.stringify(hierarchy)
                },
                success: function(resp) {
                    var ok = resp && (resp.ok === true || resp.success === true);
                    if (!ok && resp && resp.ok === false) {
                        if (typeof $.notify === 'function') { $.notify('Error al guardar orden', 'error'); }
                        else { alert('Error al guardar orden'); }
                        return;
                    }
                    if (typeof $.notify === 'function') { $.notify('Orden guardado', 'success'); }
                    else { alert('Orden guardado'); }
                },
                error: function(xhr) {
                    var msg = 'Error al guardar orden';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) { msg = xhr.responseJSON.message; }
                    if (typeof $.notify === 'function') { $.notify(msg, 'error'); }
                    else { alert(msg); }
                }
            });
        };
    })();
</script>