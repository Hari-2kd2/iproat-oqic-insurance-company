@extends('admin.master')
@section('content')
    <div id="preloader" class="preloader hidden"></div>
@section('title')
    @lang('attendance.calculate_attendance')
@endsection
<style>
    .departmentName {
        position: relative;
    }

    #department_id-error {
        position: absolute;
        top: 66px;
        left: 0;
        width: 100%;
        width: 100%;
        height: 100%;
    }

    .fade1 {
        background-color: white;
        opacity: 0.9;
        top: 0px;
        left: 0px;
        right: 0px;
        bottom: 0px;
        margin: 0px;
        width: 100%;
        height: auto;
        position: fixed;
        z-index: 1040;
        display: none;
    }

    .fade1 .spin {
        position: absolute;
        top: 48%;
        left: 48%;
    }
</style>
<script>
    jQuery(function() {
        $("#employeeAttendance").validate();
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

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="row">
                            <div id="searchBox">
                                <div class="col-md-1"> </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label" for="branch_id">@lang('common.branch')<span
                                                class="validateRq">*</span></label>
                                        <select name="branch_id" class="form-control required branch_id select2" multiple>
                                            @foreach (multipleBranchList() as $key => $value)
                                                <option value="{{ $key }}"
                                                    @if ($branch_id == $key) {{ 'selected' }} @endif>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label" for="department_id">@lang('common.department'):</label>
                                        <select name="department_id[]" class="form-control department_id  select2"
                                            multiple>
                                            <option selected disabled hidden>--- @lang('common.all') ---</option>
                                            @foreach ($departmentList as $value)
                                                <option value="{{ $value->department_id }}"
                                                    @if ($value->department_id == $department_id) {{ 'selected' }} @endif>
                                                    {{ $value->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <label class="control-label" for="email">@lang('common.from_date')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control dateField required from_date" readonly
                                            placeholder="@lang('common.from_date')" name="from_date"
                                            value="@if (isset($from_date)) {{ $from_date }}@else{{ dateConvertDBtoForm(date('Y-m-d')) }} @endif">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <label class="control-label" for="email">@lang('common.to_date')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control dateField required to_date" readonly
                                            placeholder="@lang('common.to_date')" name="to_date"
                                            value="@if (isset($to_date)) {{ $to_date }}@else{{ dateConvertDBtoForm(date('Y-m-d')) }} @endif">
                                    </div>
                                </div>

                                <div class="col-md-1">
                                    <div class="form-group">
                                        <input type="submit" id="filter"
                                            style="margin-top: 25px;height:36px;width: 150px;"
                                            class="btn btn-info btn-md generateReport" value="Recompute">
                                    </div>
                                </div>
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
    $(".generateReport").click(function() {
        var from_date = $('.from_date').val();
        var to_date = $('.to_date').val();
        var department_id = $('.department_id').val();
        var branch_id = $('.branch_id').val();

        // if (branch_id == null || branch_id == '') {
        //     swal({
        //         title: "Oops!",
        //         text: "Kindly select mandatory fields.",
        //         type: "error"
        //     });
        //     return;
        // }
        
        window.open(
            "{{ route('calculateAttendance.calculateAttendance', ['ajaxcall' => 1]) }}" + '&from_date=' +
            from_date + '&to_date=' + to_date + '&department_id=' + department_id,
            '_blank');

    });
</script>


<?php if (isset($_GET['ajaxcall'])){ ?>

<script>
    var from_date = $('.from_date').val();
    var to_date = $('.to_date').val();
    var department_id = $('.department_id').val();
    $('.fade1').show();

    $.ajax({
        type: "GET",
        url: "{{ route('generateReport.generateReport') }}",
        data: {
            from_date: from_date,
            to_date: to_date,
            department_id: department_id,
            _token: $('input[name=_token]').val()
        },
        success: function(data) {
            $('.fade1').hide();
            swal({
                title: "Attendance Report!",
                text: "Success! Operation has been completed.",
                type: "success"
            });
            setTimeout(() => {
                window.close();
            }, 5000);

        }
    });
</script>
<?php } ?>
@endsection
