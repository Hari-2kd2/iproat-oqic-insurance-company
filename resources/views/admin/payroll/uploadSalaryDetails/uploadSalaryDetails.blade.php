@extends('admin.master')
@section('content')
@section('title')
    @lang('payroll_setup.upload_salary_details')
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
                @if (count($errors) > 0)
                    <div class="alert alert-danger alert-block" style="margin-top: 12px;">
                        Upload Validation
                        Error<button type="button" class="close" data-dismiss="alert">x</button>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                    </div>
                @endif
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block" style="margin-top: 12px;">
                        <button type="button" class="close" data-dismiss="alert">x</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
                @if ($message = Session::get('danger'))
                    <div class="alert alert-danger alert-block" style="margin-top: 12px;">
                        <button type="button" class="close" data-dismiss="alert">x</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="bg-title border" style="margin: 12px;padding:12px">
                            <div class="border col-sm-12 col-md-12">
                                <div class="border" style="margin-left: 14px;margin-right: 14px">
                                    <form action="{{ Url('uploadSalaryDetails/import') }}" method="post"
                                        enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <p class="border"><span><i class="fa fa-upload"></i></span><span
                                                style="margin-left: 12px"> Upload Excel
                                                File Here</span></p>
                                        <div class="row">
                                            <div class="col-md-9 col-sm-6"
                                                style="margin-left: 46px;margin-bottom: 2px;">
                                                <input type="file" name="select_file" class="form-control">
                                            </div>
                                            <div class="col-sm-1">
                                                <input class="btn btn-success" style="margin-top: 2px;width: 118px;"
                                                    type="submit" value="Upload">
                                            </div>
                                            <div class="text-right">
                                                @php
                                                    $path = 'app\public\templates\template1.xlsx';
                                                @endphp
                                                <a href="{{ route('uploadSalaryDetails.downloadFile') }}">
                                                    <input type="button" id="template1" class="btn btn-info template1"
                                                        value="Sample Format" type="submit"
                                                        style="margin-top: 2px;width: 180px;" />
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="panel" style="padding-top: 24px;padding-bottom: 2px;">
                            <div class="panel-body  bg-title" style="margin-left: 12px;margin-right: 12px;">
                                <div class="row" style="margin-top: 10px;">
                                    <div class="col-md-0" style=""></div>
                                    <div class="col-md-6 bg-white" style="padding: 2px;margin-left: 72px">
                                        {{-- <label class="control-label" for="email">@lang('common.month')
                                            :</label> --}}
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control monthField required" readonly
                                                placeholder="Select Month" name="month"
                                                value="@if (isset($month)) {{ $month }}@else {{ 'Month' }} @endif">
                                        </div>
                                    </div>
                                    <div class="text-right" style="margin-right: 12px;margin-top: 4px;">
                                        <div class="" style="margin-bottom: 12px;padding-right: 2px;">
                                            @if (count($results) > 0)
                                                <a href="{{ Url('uploadSalaryDetails/export', ['type' => 'xlsx']) }}"
                                                    style="width: 200px;" class="btn btn-info"><span
                                                        class="text-white">Overall
                                                        report - XLSX</span></a>
                                                {{-- <a href="{{ Url('uploadSalaryDetails/export', ['type' => 'csv']) }}"
                                                   style="width: 200px;" class="btn btn-info"><span class="text-white">Overall
                                                        report - CSV</span></a> --}}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="data">
                                @include('admin.payroll.uploadSalaryDetails.pagination')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('page_scripts')
<script>
    $(function() {

        $('.data').on('click', '.pagination a', function(e) {
            getData($(this).attr('href').split('page=')[1]);
            e.preventDefault();
        });

        $(".monthField").change(function() {
            getData(1);
        });


    });

    function getData(page) {
        var monthField = $('.monthField').val();
        $.ajax({
            url: '?page=' + page + "&monthField=" + monthField,
            datatype: "html",
        }).done(function(data) {
            $('.data').html(data);
            $("html, body").animate({
                scrollTop: 0
            }, 150);
        }).fail(function() {
            $.toast({
                heading: 'Warning',
                text: 'Something Error Found !, data could not be loaded. !',
                position: 'top-right',
                loaderBg: '#ff6849',
                icon: 'success',
                hideAfter: 3000,
                stack: 6
            });
        });

    }
</script>
@endsection
