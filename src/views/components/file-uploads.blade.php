<?php
    
    // before render
    if (array_key_exists('beforeRender', $component)) {
        $method = $component['beforeRender'];
        $component = $controller::$method($component, @$item);
    }

    $translationFile = 'messages.';
    if (array_key_exists('translationFile', $config)) {
        $translationFile = $config['translationFile'];
    }

    $title = $component['title'] ?? 'files';
    $help = isset($component['help']) ? ucfirst(trans($translationFile . $component['help'])) : '';

    $object = $component['object'] ?? ($component['table'] ?? 'generic');
    $rowId = $component['rowId'] ?? ($component['row_id'] ?? ($item->id ?? null));
    $category = $component['category'] ?? null;

    $baseUrl = rtrim($component['url'] ?? '/core/files', '/');
    $multiple = array_key_exists('multiple', $component) ? (bool) $component['multiple'] : true;
    $allowUpload = array_key_exists('allowUpload', $component) ? (bool) $component['allowUpload'] : true;
    $allowDelete = array_key_exists('allowDelete', $component) ? (bool) $component['allowDelete'] : true;
    $allowMain = array_key_exists('allowMain', $component) ? (bool) $component['allowMain'] : true;
    $allowOrder = array_key_exists('allowOrder', $component) ? (bool) $component['allowOrder'] : true;
    $allowInfo = array_key_exists('allowInfo', $component) ? (bool) $component['allowInfo'] : true;

    $accept = $component['accept'] ?? ($component['extensions'] ?? '');
    $maxFiles = $component['maxFiles'] ?? null;
    $maxSize = $component['maxSize'] ?? null;

    $uid = 'sfwfu_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $object . '_' . ($rowId ?? 'new'));
    $viewBase = $component['viewBase'] ?? '/files';
    $viewOrigin = $component['origin'] ?? $object;

    $cfg = [
        'baseUrl' => $baseUrl,
        'object' => $object,
        'rowId' => $rowId,
        'category' => $category,
        'multiple' => $multiple,
        'allowUpload' => $allowUpload,
        'allowDelete' => $allowDelete,
        'allowMain' => $allowMain,
        'allowOrder' => $allowOrder,
        'allowInfo' => $allowInfo,
        'accept' => $accept,
        'maxFiles' => $maxFiles,
        'maxSize' => $maxSize,
        'viewBase' => $viewBase,
        'viewOrigin' => $viewOrigin,
    ];
?>

<div class="form-group mb-3 sfw-file-uploads" id="{{ $uid }}" data-config='@json($cfg)'>
    <label <?php echo $help != '' ? 'title="'.e($help).'" class="sfwcomponent-help-text"' : ''; ?>>{{ ucfirst(trans($translationFile.$title)) }}:</label>

    <div class="sfw-file-uploads-list row g-3 mb-3"></div>

    <!-- Modal for file info (loaded via modals.load) -->
    <div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                </div>
                <div class="modal-body">
                    <!-- Content loaded by modals.load -->
                </div>
            </div>
        </div>
    </div>

    @if($allowUpload)
        <div class="sfw-file-uploads-upload card card-body bg-light">
            <div class="mb-2">
                <input type="file" class="form-control sfw-file-uploads-input" {{ $multiple ? 'multiple' : '' }} @if($accept) accept="{{ $accept }}" @endif>
            </div>
            @if($maxSize || $maxFiles || $accept)
                <div class="small text-muted mb-2">
                    @if($accept) <span>{{ ucfirst(trans('sfwcomponent::allowed')) }}: {{ $accept }}</span> @endif
                    @if($maxSize) <span class="ml-2">{{ ucfirst(trans('sfwcomponent::max_size')) }}: {{ $maxSize }}</span> @endif
                    @if($maxFiles) <span class="ml-2">{{ ucfirst(trans('sfwcomponent::max_files')) }}: {{ $maxFiles }}</span> @endif
                </div>
            @endif
            <div>
                <button type="button" class="btn btn-primary sfw-file-uploads-btn-upload">{{ ucfirst(trans('messages.upload')) }}</button>
            </div>
        </div>
    @endif
</div>

<script>
(function() {
    var root = document.getElementById(@json($uid));
    if (!root || root.dataset.sfwInit === '1') return;
    root.dataset.sfwInit = '1';

    var cfg = {};
    try { cfg = JSON.parse(root.dataset.config || '{}'); } catch (e) {}
    var listEl = root.querySelector('.sfw-file-uploads-list');
    var inputEl = root.querySelector('.sfw-file-uploads-input');
    var uploadBtn = root.querySelector('.sfw-file-uploads-btn-upload');

    function base() { return (cfg.baseUrl || '/core/files').replace(/\/$/, ''); }
    function csrf() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : (@json(csrf_token()));
    }

    function notify(type, msg) {
        if (window.alerts && alerts.show) { alerts.show(type, msg); return; }
        if (type === 'ko' || type === 'error') alert(msg || 'Error'); else console.log(msg || 'ok');
    }

    function render(files) {
        if (!listEl) return;
        listEl.innerHTML = '';
        if (!files || !files.length) return;
        files.forEach(function(f) {
            var id = f.id;
            var col = document.createElement('div');
            col.className = 'col-md-3 col-sm-4 col-6 text-center';
            var card = document.createElement('div');
            card.className = 'border rounded p-2 h-100' + (parseInt(f.is_main,10) ? ' border-primary' : '');
            var img = document.createElement('img');
            img.className = 'img-fluid mb-2';
            img.style.maxHeight = '128px';
            img.src = (cfg.viewBase || '/files') + '/' + encodeURIComponent(id) + '/view?origin=' + encodeURIComponent(cfg.viewOrigin || cfg.object || '') + '&size=128_128';
            card.appendChild(img);
            if (parseInt(f.is_main,10)) {
                var badge = document.createElement('div');
                badge.className = 'mb-2';
                badge.innerHTML = '<span class="badge bg-primary">{{ ucfirst(trans("messages.main")) }}</span>';
                card.appendChild(badge);
            }
            var actions = document.createElement('div');
            actions.className = 'd-flex justify-content-center gap-2 flex-wrap';
            if (cfg.allowInfo) {
                var bInfo = document.createElement('button');
                bInfo.type = 'button';
                bInfo.className = 'btn btn-sm btn-outline-secondary';
                bInfo.title = @json(ucfirst(trans('messages.edit')));
                bInfo.innerHTML = '<i class="ti ti-edit"></i>';
                (function(fileId){ bInfo.onclick = function() { openInfo(fileId); }; })(id);
                actions.appendChild(bInfo);
            }
            if (cfg.allowOrder) {
                var inp = document.createElement('input');
                inp.type = 'number';
                inp.className = 'form-control form-control-sm';
                inp.style.width = '80px';
                inp.value = (f.sort_order != null ? f.sort_order : 0);
                (function(fileId, inputEl){ inputEl.onchange = function() { setOrder(fileId, inputEl.value); }; })(id, inp);
                actions.appendChild(inp);
            }
            if (cfg.allowMain && !parseInt(f.is_main,10)) {
                var bMain = document.createElement('button');
                bMain.type = 'button';
                bMain.className = 'btn btn-sm btn-outline-secondary';
                bMain.title = @json(ucfirst(trans('messages.set_main')));
                bMain.innerHTML = '<i class="ti ti-check"></i>';
                (function(fileId){ bMain.onclick = function() { setMain(fileId); }; })(id);
                actions.appendChild(bMain);
            }
            if (cfg.allowDelete) {
                var bDel = document.createElement('button');
                bDel.type = 'button';
                bDel.className = 'btn btn-sm btn-outline-danger';
                bDel.title = @json(ucfirst(trans('messages.delete')));
                bDel.innerHTML = '<i class="ti ti-trash"></i>';
                (function(fileId){ bDel.onclick = function() { del(fileId); }; })(id);
                actions.appendChild(bDel);
            }
            card.appendChild(actions);
            col.appendChild(card);
            listEl.appendChild(col);
        });
    }

    function load() {
        var url = base() + '/list?object=' + encodeURIComponent(cfg.object||'') + '&rowId=' + encodeURIComponent(cfg.rowId||'') + '&category=' + encodeURIComponent(cfg.category||'');
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(function(r){ return r.json(); })
            .then(function(data){ render(data.files || data || []); })
            .catch(function(){ notify('ko', 'Error'); });
    }

    function post(url, data, isForm) {
        var opts = { method: 'POST', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } };
        if (isForm) { opts.body = data; }
        else { opts.headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(data || {}); }
        return fetch(url, opts).then(function(r){ return r.json().catch(function(){ return {success:r.ok}; }); });
    }

    function del(id) {
        if (!confirm(@json(ucfirst(trans('messages.are_you_sure'))))) return;
        post(base() + '/' + encodeURIComponent(id) + '/delete', {}).then(function(){ load(); }).catch(function(){ notify('ko','Error'); });
    }
    function setMain(id) { post(base() + '/' + encodeURIComponent(id) + '/main', {}).then(function(){ load(); }); }
    function setOrder(id, order) { post(base() + '/' + encodeURIComponent(id) + '/order', {order: order, sort_order: order}).then(function(res){ if(res && res.success===false) notify('ko', res.message||'Error'); else load(); }); }
    function openInfo(id) {
        var url = base() + '/' + encodeURIComponent(id) + '/info';
        var title = @json(ucfirst(trans('messages.edit')));
        modals.show(url, '#modal1', title, {});
    }

    function upload() {
        if (!inputEl || !inputEl.files || !inputEl.files.length) return;
        var fd = new FormData();
        fd.append('object', cfg.object || '');
        fd.append('rowId', cfg.rowId || '');
        if (cfg.category) fd.append('category', cfg.category);
        if (cfg.maxSize) fd.append('maxSize', cfg.maxSize);
        if (cfg.accept) fd.append('accept', cfg.accept);
        if (cfg.maxFiles) fd.append('maxFiles', cfg.maxFiles);
        for (var i=0; i<inputEl.files.length; i++) { fd.append('file[]', inputEl.files[i]); }
        post(base() + '/upload', fd, true).then(function(res){
            if (res && res.success === false) { notify('ko', res.message || 'Error'); return; }
            inputEl.value = '';
            load();
        }).catch(function(){ notify('ko','Error'); });
    }

    if (uploadBtn) uploadBtn.addEventListener('click', upload);
    load();

    // expose for modal save callbacks to refresh
    root.sfwFileUploadsReload = load;
    window.sfwFileUploadsReload = window.sfwFileUploadsReload || {};
    window.sfwFileUploadsReload[@json($uid)] = load;
})();
</script>