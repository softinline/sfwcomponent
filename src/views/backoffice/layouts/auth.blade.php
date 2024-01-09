<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>SFW | Backoffice</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdn.softinline.com/fontawesome/5.15.4/css/all.min.css">
        <!-- Line Awesome -->
        <link rel="stylesheet" href="https://cdn.softinline.com/lineawesome/css/line-awesome.min.css">
        <!-- Admin LTE3 -->
        <link rel="stylesheet" href="https://cdn.softinline.com/templates/adminlte3/dist/css/adminlte.min.css">
        <!-- DataTable -->
        <link rel="stylesheet" href="https://cdn.softinline.com/templates/adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="https://cdn.softinline.com/templates/adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
        <!-- jquery notify -->
        <link rel="stylesheet" href="https://cdn.softinline.com/plugins/notify/css/notify.css">
        <!-- jQuery UI css -->
        <link rel="stylesheet" href="https://cdn.softinline.com/templates/adminlte3/plugins/jquery-ui/jquery-ui.min.css">
        <!-- Scrollbards -->
        <link rel="stylesheet" href="https://cdn.softinline.com/templates/adminlte3/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
        <!-- colorpicker bootstrapt -->
        <link rel="stylesheet" href="https://cdn.softinline.com/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
        <!-- jQuery 2.1.4 -->
        <script src="https://cdn.softinline.com/templates/adminlte3/plugins/jquery/jquery.min.js"></script>
        <!-- graphics -->
        <link rel="stylesheet" href="https://cdn.softinline.com/plugins/morris/morris.css">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->        
        <link rel="stylesheet" href="{{ url('vendor/softinline/sfwcomponent/backoffice.css?'.time()) }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ url('/images/favicon.ico') }}" />
        <link rel="apple-touch-icon" type="image/x-icon" href="{{ url('/images/favicon.ico') }}" />
    </head>
    <body class="hold-transition login-page">
        @include('sfwcomponent::backoffice.partials.flash-messages')
        @yield('content')
        <!-- Bootstrap 3.3.5 -->
        <script src="https://cdn.softinline.com/templates/adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- jQuery UI 1.12.1 -->
        <script src="https://cdn.softinline.com/templates/adminlte3/plugins/jquery-ui/jquery-ui.min.js"></script>
        <!-- DataTable -->
        <script src="https://cdn.softinline.com/templates/adminlte3/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="https://cdn.softinline.com/templates/adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.softinline.com/templates/adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.softinline.com/templates/adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <!-- Notify -->
        <script src="https://cdn.softinline.com/plugins/notify/js/notify.js"></script>
        <!-- Loading overlay -->
        <script src="https://cdn.softinline.com/plugins/jquery-loading-overlay-1.4.0/src/loadingoverlay.min.js"></script>
        <!-- scrollbars -->
        <script src="https://cdn.softinline.com/templates/adminlte3/plugins/overlayScrollbars/js/OverlayScrollbars.min.js"></script>
        <!-- AdminLTE App -->
        <script src="https://cdn.softinline.com/templates/adminlte3/dist/js/adminlte.min.js"></script>
        <!-- select2 lib -->
        <link rel="stylesheet" href="https://cdn.softinline.com/plugins/select2/select2.min.css">
        <script src="https://cdn.softinline.com/plugins/select2/select2.min.js"></script>
        <!-- datepicker lib -->
        <link rel="stylesheet" href="https://cdn.softinline.com/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css">
        <script defer src="https://cdn.softinline.com/plugins/bootstrap-datetimepicker/js/moment-with-locales.min.js"></script>
        <script defer src="https://cdn.softinline.com/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
        <!-- ckeditor -->
        <script src="https://cdn.softinline.com/plugins/ckeditor/ckeditor.js"></script>
        <!-- grpahics lib -->
        <script src="https://cdn.softinline.com/plugins/morris/morris.min.js"></script>
        <script src="https://cdn.softinline.com/plugins/raphael/raphael-min.js"></script>
        <!-- Swal -->
        <script src="https://cdn.softinline.com/plugins/swal/swal.min.js"></script>
        <!-- bootstrap color picker -->
        <script defer src="https://cdn.softinline.com/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
        <!-- ckeditor -->
        <script src="https://cdn.softinline.com/lib/alerts.js"></script>
        <script src="https://cdn.softinline.com/lib/modals.js"></script>
        <script src="https://cdn.softinline.com/lib/swal.js"></script>
        <script src="{{ url('vendor/softinline/sfwcomponent/backoffice.js?'.time()) }}"></script>
        <!--{{-- <script src="{{ url('js/admin.js') }}"></script> --}}
        <script src="{{ url('js/i18n/'.\App::getLocale().'.js?'.time()) }}"></script>
        <script src="{{ url('js/i18n.js?'.time()) }}"></script>-->
        <script src="{{ url('vendor/softinline/sfwcomponent/sfwcomponent.js?'.time()) }}"></script>
        @yield('script')
        <script>
            backoffice.ajax = false;
            backoffice.locale = '{{ \App::getLocale() }}';
            backoffice.init();
            sfwcomponent.locale = backoffice.locale;
            sfwcomponent.ajax = true;
            sfwcomponent.redirectToTab('sfwTab');
            sfwcomponent.init();
        </script>
    </body>
</html>