<?php   
    $sfwObjects = \Softinline\SfwComponent\Models\SfwObject::select()
        ->where('type', '=', 1)
        ->where('system', '=', 1)
        ->whereNull('sfw_object_id')
        ->orderBy('order')
        ->get();
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/') }}" class="brand-link">
        <span class="brand-text">SFW - BACK</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <?php foreach($sfwObjects as $sfwObject) { ?>                
                    <li class="nav-item treeview">
                        <a href="{{ url($sfwObject->url) }}"" class="nav-link">
                            <i class="nav-icon las la-circle"></i>
                            <p>{{ $sfwObject->object }}</p>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</aside>