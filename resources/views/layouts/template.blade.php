<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.head')

<body data-sidebar="dark" comment-class="sidebar-enable vertical-collpsed">

<!-- Begin page -->
<div id="layout-wrapper">

      @include('layouts.header')
    <!-- ========== Left Sidebar Start ========== -->
      @include('layouts.sidebar')

    <!-- Left Sidebar End -->

    

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">
                 @yield('content')
            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

         @include('layouts.footer')

    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->

<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>

<!-- JAVASCRIPT -->
    @include('layouts.footer-js')
    <script type="text/javascript">

@if (count($errors) > 0)
@foreach ($errors->all() as $error)
error("{{$error}}", 'Input error');
@endforeach
@elseif (Session::has('warning'))
warning("{{ Session::get('warning') }}");
@elseif (Session::has('success'))
success("{{ Session::get('success') }}");
@elseif (Session::has('error'))
error("{{ Session::get('error') }}");
@elseif (Session::has('info'))
info(`{{ Session::get('info') }}`);


@elseif (isset($warning))
warning("{{ $warning }}");
@elseif (isset($success))
success("{{ $success }}");
@elseif (isset($error))
error("{{ $error }}");
@elseif (isset($info))
info("{{ $info }}");

@else
@endif


</script>


    @yield('js')

</body>

</html>