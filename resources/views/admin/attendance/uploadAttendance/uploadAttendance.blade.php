@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.upload_attendance')
@endsection
<style>
    body {

        font-family: 'Nunito', sans-serif;

    }

    #hideMe {
        -webkit-animation: seconds 1.0s forwards;
        -webkit-animation-iteration-count: 1;
        -webkit-animation-delay: 3s;
        animation: seconds 1.0s forwards;
        animation-iteration-count: 1;
        animation-delay: 3s;
        position: relative;
    }

    @-webkit-keyframes seconds {
        0% {
            opacity: 1;
        }

        100% {
            opacity: 0;
            left: -9999px;
            position: absolute;
        }
    }

    @keyframes seconds {
        0% {
            opacity: 1;
        }

        100% {
            opacity: 0;
            left: -9999px;
            position: absolute;
        }
    }

    th {
        background-color: rgb(65, 179, 249);
        color: white;
    }

    #loader {
        border: 12px solid #f3f3f3;
        border-radius: 50%;
        border-top: 2px solid rgb(65, 179, 249);
        width: 70px;
        height: 70px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }

    .center {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: auto;
    }

</style>
<script>
    jQuery(function() {
        $("#uploadEmployeeAttendance").validate();
    });
</script>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>
    <div class="row container-fluid">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="bg-title border" style="margin: 12px;padding:12px">
                            <div class="border col-sm-12 col-md-12">
                                <div class="border" style="margin-left: 14px;margin-right: 14px">
                                    <form action="{{ Url('uploadAttendance/import') }}" class=""
                                        method="post" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <p class="border"><span><i class="fa fa-upload"></i></span><span
                                                style="margin-left: 8px"> Upload Excel
                                                File Here</span></p>
                                        <div class="row">
                                            <div class="col-sm-4 col-md-4"
                                                style="margin-left: 46px;  margin-bottom: 2px;">
                                                <input type="file" name="select_file" class="form-control">
                                            </div>
                                            <div class="col-sm-1" id="uploadAttendance">
                                                <input class="btn btn-success" style="margin-top: 2px; width: 114px;" type="submit"
                                                    value="Upload">
                                            </div>
                                            <div class="text-right">
                                                <a href="{{ route('uploadAttendance.downloadFile') }}">
                                                    <input type="button" id="attendance_template"
                                                        class="btn btn-info attendance_template" value="Sample Format"
                                                        type="submit" style="margin-top: 2px; width: 180px;" />
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                    @if (count($errors) > 0)
                                        <div class="alert alert-danger alert-block"
                                            style="margin-right: 46px;  margin-top: 12px">Upload Validation
                                            Error<br><br></div>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if ($message = Session::get('success'))
                                        <div class="alert alert-success alert-block"
                                            style="margin-right: 46px;  margin-top: 12px">
                                            <button type="button" class="close" data-dismiss="alert">x</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    @if ($message = Session::get('danger'))
                                        <div class="alert alert-danger alert-block"
                                            style="margin-right: 46px;  margin-top: 12px">
                                            <button type="button" class="close" data-dismiss="alert">x</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="data">
                            {{-- <div id="loader" class="center"></div> --}}
                            {{-- <div class="loader" id="AjaxLoader" style="display:none;">
                                <div class="strip-holder">
                                    <div class="strip-1"></div>
                                    <div class="strip-2"></div>
                                    <div class="strip-3"></div>
                                </div>
                            </div> --}}
                            @include('admin.attendance.uploadAttendance.pagination')
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="row text-right" style="margin-bottom: 15px; margin-right: 16;">
                <a href="{{ route('uploadAttendance/export', ['type' => 'xlsx']) }}" class="btn btn-success"
                    style="margin-right: 15px;">Download - XLSX</a>
                <a href="{{ route('uploadAttendance/export', ['type' => 'csv']) }}" class="btn btn-success"
                    style="margin-right: 15px; ">Download - CSV</a>
            </div> --}}


        </div>
    </div>
</div>
<script>
    // $(document)
    //     .ajaxStart(function() {
    //         $('#AjaxLoader').show();
    //     })
    //     .ajaxStop(function() {
    //         $('#AjaxLoader').hide();
    //     });

    document.onreadystatechange = function() {
        if (document.readyState !== "complete") {
            document.querySelector(
                "body").style.visibility = "hidden";
            document.querySelector(
                "#loader").style.visibility = "visible";
        } else {
            document.querySelector(
                "#loader").style.display = "none";
            document.querySelector(
                "body").style.visibility = "visible";
        }
    };
</script>
@endsection
