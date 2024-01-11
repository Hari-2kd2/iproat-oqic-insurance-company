<!DOCTYPE html>
<html lang="en">
@php
    $front_setting = getFrontData();
@endphp

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{!! asset('icon.png') !!}" type="image/x-icon" />
    <!-- <link rel="shortcut icon" href="{!! asset('admin_assets/img/oqic_logo.png') !!}" type="image/x-icon" />  -->
    <title>Pro-People</title>
    <title>@yield('title')</title>

    @include('admin.layout.css')
    @include('admin.layout.color')
    @include('admin.layout.custom')

    <!-- Scripts -->
    <!-- <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>;
    </script> -->

   <!-- <script type="text/javascript">
        var base_url = "{{ url('/') . '/' }}";
    </script> -->

   <!-- @if (!auth()->guest())
        <script>
            window.Laravel.userId = <?php echo auth()->user()->user_id; ?>;
        </script>
    @endif -->

</head>


<body class="fix-header" onload="addMenuClass(); zoom();">
    <!-- ============================================================== -->
    <!-- Preloader -->
    <!-- ============================================================== -->

    <div class="fade1" style="width:100%;height:100%;display:none;background-color:white;">
        <img src="{{ asset('/images/timer.gif') }}" class='center' style='transform: scale(0.06);'>
    </div>

    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2"
                stroke-miterlimit="10" />
        </svg>
    </div>

    <!-- ============================================================== -->
    <!-- Wrapper -->
    <!-- ============================================================== -->
    <div id="wrapper">
        @include('admin.layout.navbar')
        @include('admin.layout.sidebar')
        <div id="page-wrapper" style="border-bottom-right-radius: 6px;">
            @yield('content')
        </div>
        <!--  <footer class="footer text-center" style="font-size: 12px">
            {{ date('Y') }} &copy; <b style="padding-right: 4px;padding-left: 4px">
                <a href="{{ url('dashboard') }}">PRO-PEOPLE</a>
            </b> All rights reserved.
        </footer> -->
    </div>
    @include('admin.layout.javascript')
    @yield('page_scripts')
</body>

</html>
