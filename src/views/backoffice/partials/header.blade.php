<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>  
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-user"></i> 
                welcome
            </a>
            <?php if(\Session::get('sfw-user-logged')) { ?>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <a href="{{ url('sfw/auth/logoff') }}" class="dropdown-item">
                        <i class="las la-sign-out-alt mr-2"></i> logoff
                    </a>
                </div>
            <?php } ?>
        </li>
    </ul>
</nav>