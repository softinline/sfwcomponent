<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>SFW</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">        
        <!-- jQuery -->
        <script src="{{ url(\Config::get('app.cdn_url').'/plugins/jquery/jquery-3.6.0.min.js') }}"></script>
        <!-- Bootstrao -->
        <script src="{{ url(\Config::get('app.cdn_url').'/bootstrap/bootstrap-5.0.2/js/bootstrap.min.js') }}"></script>
        <!-- jQuery UI 1.12.1 -->
        <script src="{{ url(\Config::get('app.cdn_url').'/templates/adminlte3/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
        <!-- DataTable -->        
        <script defer src="{{ url(\Config::get('app.cdn_url').'/templates/adminlte3/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script defer src="{{ url(\Config::get('app.cdn_url').'/templates/adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script defer src="{{ url(\Config::get('app.cdn_url').'/templates/adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script defer src="{{ url(\Config::get('app.cdn_url').'/templates/adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>        
        <!-- Notify -->
        <script defer src="{{ url(\Config::get('app.cdn_url').'/plugins/notify/js/notify.js') }}"></script>        
        <!-- Loading overlay -->
        <script defer src="{{ url(\Config::get('app.cdn_url').'/plugins/jquery-loading-overlay-1.4.0/src/loadingoverlay.min.js') }}"></script>
        <!-- Swal -->
        <script defer src="{{ url(\Config::get('app.cdn_url').'/plugins/swal/swal.min.js') }}"></script>
        <!-- Datetime picker -->
        <!--<script defer src="{{ url(\Config::get('app.cdn_url').'/plugins/bootstrap-datetimepicker/js/moment-with-locales.min.js') }}"></script>
        <script defer src="{{ url(\Config::get('app.cdn_url').'/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>-->
        <!-- app -->
        <script defer src="{{ url(\Config::get('app.cdn_url').'/lib/alerts.js?'.time()) }}"></script>
        <script defer src="{{ url(\Config::get('app.cdn_url').'/lib/modals.js?'.time()) }}"></script>
        <script defer src="{{ url(\Config::get('app.cdn_url').'/lib/swal.js?'.time()) }}"></script>
        <script src="{{ url('vendor/softinline/sfwcomponent/sfwcomponent.js?'.time()) }}"></script>
        <script src="{{ url('back/js/i18n/'.\App::getLocale().'.js?'.time()) }}"></script>
        <script src="{{ url('back/js/i18n.js?'.time()) }}"></script>
        <script src="{{ url('back/js/app.js?'.time()) }}"></script>
        <script>
            $(function() {
                // set app and init
                app.ajax = false;
                app.locale = '{{ \App::getLocale() }}';
                app.init();

                // set sfwcomponent and init
                sfwcomponent.mapboxAccessToken = 'pk.eyJ1IjoicHBlcGUiLCJhIjoiY2w0Z3Rscm93MDI5ODNicGFzaDhuZWFjaCJ9.170WjgIKNHPhFg4uyQoJrw';
                sfwcomponent.locale = app.locale;
                sfwcomponent.init();
            })
        </script>
        <!-- Bootstrap -->
        <link rel="stylesheet" href="{{ url(\Config::get('app.cdn_url').'/bootstrap/bootstrap-5.0.2/css/bootstrap.min.css') }}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ url(\Config::get('app.cdn_url').'/fontawesome/5.15.4/css/all.min.css') }}">
        <!-- Line Awesome -->
        <link rel="stylesheet" href="{{ url(\Config::get('app.cdn_url').'/lineawesome/css/line-awesome.min.css') }}">
        <!-- DataTable -->
        <link rel="stylesheet" href="{{ url(\Config::get('app.cdn_url').'/templates/adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ url(\Config::get('app.cdn_url').'/templates/adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <!-- jquery notify -->
        <link rel="stylesheet" href="{{ url(\Config::get('app.cdn_url').'/plugins/notify/css/notify.css') }}">        
        <!-- jQuery UI css -->
        <link rel="stylesheet" href="{{ url(\Config::get('app.cdn_url').'/templates/adminlte3/plugins/jquery-ui/jquery-ui.min.css') }}">
        <!-- datetimepicker -->
        <link rel="stylesheet" href="{{ url(\Config::get('app.cdn_url').'/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- sfwcomponent -->
        <link rel="stylesheet" href="{{ url('vendor/softinline/sfwcomponent/sfwcomponent.css?'.time()) }}">
        <link rel="stylesheet" href="{{ url('back/css/general.css') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ url('/images/favicon.png') }}" />
        <link rel="apple-touch-icon" type="image/x-icon" href="{{ url('/images/favicon.png') }}" />
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">                
        <div class="wrapper">                        
            <div class="content-wrapper">
                <section class="content">
                    <div class="container-fluid">                        
                        @include('sfwcomponent::backoffice.partials.flash-messages')
                        @include('sfwcomponent::backoffice.partials.header')
                        @yield('content')
                    </div>
                </section>
            </div>            
        </div>
        @yield('script')
    </body>
</html>