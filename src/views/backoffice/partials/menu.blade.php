@php
    $sfwObjects = \Softinline\SfwComponent\Models\SfwObject::query()
        ->where('system', 1)
        ->where('type', 'menu')
        ->where('is_active', 1)
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->get();
@endphp
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/') }}" class="brand-link">
        <span class="brand-text">SFW - BACKOFFICE</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @foreach($sfwObjects as $sfwObject)
                    <li class="nav-item">
                        <a href="{{ url($sfwObject->route ?? '#') }}" class="nav-link">
                            <i class="nav-icon las la-circle"></i>
                            <p>{{ $sfwObject->name }}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</aside>