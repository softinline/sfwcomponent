<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <?php $sfwPrefix = trim((string) config('sfw.route_prefix'), '/'); ?>
        <?php if(\Session::get('sfw-user-logged')) { ?>
            <li class="nav-item">
                <span class="nav-link"><i class="far fa-user"></i> {{ \Session::get('sfw-user-name', 'admin') }}</span>
            </li>
            <li class="nav-item">
                <a href="{{ url($sfwPrefix . '/auth/logoff') }}" class="nav-link">
                    <i class="las la-sign-out-alt"></i> {{ trans('sfwcomponent::logout') }}
                </a>
            </li>
        <?php } ?>
    </ul>
</nav>