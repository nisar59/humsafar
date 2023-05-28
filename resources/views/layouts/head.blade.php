<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{Settings()->portal_name}}-@yield('title')</title>



    <link href="{{asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">

    <!-- Responsive datatable examples -->
    <link href="{{asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">


    <link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

    <link href="{{asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/libs/jquery-ui/themes/base/jquery-ui.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/libs/jquery-ui/themes/base/autocomplete.css')}}" rel="stylesheet">
    <link href="{{asset('assets/libs/jquery-ui/themes/base/theme.css')}}" rel="stylesheet">

    <!-- Bootstrap Css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" id="bootstrap-style-comment" rel="stylesheet" type="text/css">
    <!-- Icons Css -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css">
    <!-- App Css-->
    <link href="{{asset('assets/css/app.min.css')}}" id="app-style-comment" rel="stylesheet" type="text/css">

    <link rel='shortcut icon' type='image/x-icon' href="{{url('public/img/settings/'.Settings()->portal_favicon)}}" />

    <link rel="stylesheet" href="{{asset('assets/izitoast/css/iziToast.min.css')}}">
    <link href="{{asset('assets/libs/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/context-menu/jquery.contextMenu.css')}}" rel="stylesheet" type="text/css" />



    <link href="{{asset('assets/css/custom.css')}}" rel="stylesheet" type="text/css">



    @yield('css')
</head>