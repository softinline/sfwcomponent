<div class="dropdown  me-2">
    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="las la-filter"></i> {{ ucfirst(trans('messages.type')) }}
    </a>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <li><a class="dropdown-item {{ !\Request::has('type') ? 'active' : '' }}" href="javascript:void(0)" onclick="window.location = sfwcomponent.updateQueryStringParameter(window.location.toString(),'type','')">ALL</a></li>    
        <li><a class="dropdown-item {{ \Request::get('type') == 1 ? 'active' : '' }}" href="javascript:void(0)" onclick="window.location = sfwcomponent.updateQueryStringParameter(window.location.toString(),'type','1')">SMTP</a></li>
        <li><a class="dropdown-item {{ \Request::get('type') == 2 ? 'active' : '' }}" href="javascript:void(0)" onclick="window.location = sfwcomponent.updateQueryStringParameter(window.location.toString(),'type','2')">AMAZON</a></li>
    </ul>
</div>