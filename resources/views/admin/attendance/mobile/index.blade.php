@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.mobile_attendance')
@endsection
<script>
    jQuery(function() {
        $("#mobileAttendanceReport").validate();
    });
</script>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
                </li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div id="searchBox">
                            <div class="col-md-1"></div>
                            {{ Form::open(['route' => 'mobileAttendance.mobileAttendance', 'id' => 'mobileAttendanceReport', 'class' => 'form-horizontal']) }}
                            <div class="form-group">
                                <div>
                                    <form action="{{ url('cronjob/manualAttendance') }}">
                                        <label class="control-label col-sm-1" for="email">@lang('common.date')<span
                                            class="validateRq">*</span>:</label>
                                        <div class="col-md-3" style="margin-left: -10px">
                                            <input id="date" type="text" name="from_date"
                                                class="form-control dateField"
                                                value="@if (isset($from_date)) {{ $from_date }} @else {{ dateConvertDBtoForm(date('Y-m-d')) }} @endif"
                                                required>
                                        </div>
                                        <label class="control-label col-sm-1 text-left" style="width: 24px;margin-right:24px;" for="email">To</label>
                                        <div class="col-md-3" style="margin-left: -10px">
                                            <input id="date" type="text" name="to_date"
                                                class="form-control dateField"
                                                value="@if (isset($to_date)) {{ $to_date }} @else {{ dateConvertDBtoForm(date('Y-m-d')) }} @endif"
                                                required>
                                        </div>
                                        {{-- <div class="col-sm-6">
                                            <input type="text" class="form-control dateField" required readonly
                                                placeholder="@lang('common.date')" id="date" name="date"
                                                value="@if (isset($date)) {{ $date }}@else {{ dateConvertDBtoForm(date('Y-m-d')) }} @endif">
                                        </div> --}}
                                        <div class="col-md-1" style="margin-top: 2px;">
                                            <button type="submit" class="btn btn-info"
                                                style="width:80px">@lang('common.filter')</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                        <hr>
                        <div id="data">
                            @include('admin.attendance.mobile.pagination')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('page_scripts')
<script type="text/javascript">
    function date() {
        var date = $("#date").val();
        return date;
    }

    function gmapDetailedReport() {
        var date = $("#date").val();
        // var employee_id = employee_id;
        // $.ajax({
        //     type: "get",
        //     url: "mobile_attendance",
        //     data: {
        //         employee_id: employee_id,
        //         date: date,
        //         _token: "{{ csrf_token() }}"
        //     },
        //     success: function(response) {

        //     }
        // });
        alert(date());
    }
</script>
@endsection
