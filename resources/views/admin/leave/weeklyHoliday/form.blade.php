@extends('admin.master')
@section('content')
@section('title')
    @if (isset($editModeData))
        @lang('holiday.edit_weekly_holiday')
    @else
        @lang('holiday.add_weekly_holiday')
    @endif
@endsection

<style>
    .list ul li {
        display: flex;
        margin-left: 10px;
        margin-right: 10px;
        margin-top: 10px;
        margin-bottom: 10px;
        text-decoration: none;
        padding: 12px 16px 0px 16px;
        float: left;
        -ms-flex-align: center;
        flex-wrap: wrap;
        width: 18%;
        background-color: #2D3A41;
        /* background-color: #2C3A40; */
        border-radius: 4px;
        color: aliceblue;
        border: 2px solid #3F739A;
    }

    input[type=checkbox] {
        accent-color: #2896ff;
    }
</style>

<div class="container-fluid" style="height: 90vh">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>

            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <a href="{{ route('weeklyHoliday.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> @lang('holiday.view_weekly_holiday')</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (isset($editModeData))
                            {{ Form::model($editModeData, ['route' => ['weeklyHoliday.update', $editModeData->week_holiday_id], 'method' => 'PUT', 'files' => 'true', 'id' => 'weeklyHolidayForm', 'class' => 'form-horizontal']) }}
                        @else
                            {{ Form::open(['route' => 'weeklyHoliday.store', 'enctype' => 'multipart/form-data', 'id' => 'weeklyHoliday', 'class' => 'form-horizontal']) }}
                        @endif
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-offset-2 col-md-6">
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close"><span aria-hidden="true">×</span></button>
                                            @foreach ($errors->all() as $error)
                                                <strong>{!! $error !!}</strong><br>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if (session()->has('success'))
                                        <div class="alert alert-success alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×</button>
                                            <i
                                                class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                        </div>
                                    @endif
                                    @if (session()->has('error'))
                                        <div class="alert alert-danger alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×</button>
                                            <i
                                                class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row col-md-offset-1">
                                @if (isset($editModeData))
                                    <div class="col-md-2"></div>
                                @else
                                    <div class="col-md-2" style="margin-right: 12px;margin-left: 36px">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.department'):</label>
                                            {{ Form::select('department_id', $departmentList, old('department_id'), [
                                                'class' => 'form-control department_id required',
                                                'id' => 'department_id',
                                                'onchange' => 'getData(1);',
                                                // 'disabled' => count($employeeList) != 0 ? 'true' : 'false',
                                            ]) }}

                                        </div>
                                    </div>
                                    <div class="col-md-2" style="margin-right: 12px;">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.designation'):</label>
                                            {{ Form::select('designation_id', $designationList, old('designation_id'), [
                                                'class' => 'form-control designation_id required',
                                                'id' => 'designation_id',
                                                'onchange' => 'getData(1);',
                                                // 'disabled' => count($employeeList) != 0 ? 'true' : 'false',
                                            ]) }}
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-2" style="margin-right: 12px">
                                    <div class="form-group">
                                        <label for="exampleInput">Holiday<span class="validateRq">*</span>:</label>
                                        {{ Form::select('day_name', $weekList, old('day_name'), ['class' => 'form-control day_name required', 'id' => 'day_name', 'onchange' => 'getData(1);']) }}
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-right: 12px" hidden>
                                    <div class="form-group">
                                        <label for="exampleInput">Status</label>
                                        {{ Form::select(
                                            'status',
                                            ['' => '---- Please select ----', '0' => 'In Completed', '1' => 'Completed'],
                                            'status',
                                            [
                                                'class' => 'form-control status required',
                                                'id' => 'status',
                                            ],
                                        ) }}
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-right: 12px">
                                    <div class="form-group">
                                        <label for="exampleInput">Month<span class="validateRq">*</span>:</label>
                                        <input type="text" class="form-control month" placeholder="@lang('common.month')"
                                            id="month" name="month"
                                            value="@if (isset($month)) {{ monthConvertFormtoDB($month) }}@else {{ monthConvertFormtoDB(date('Y-m')) }} @endif"
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-md-1" style="margin-right: 12px">
                                    <div class="form-group">
                                        <label for="exampleInput" style="color: transparent">Filter</label>
                                        {{-- <input type="text" class="btn btn-info btn-sm"
                                            style="height: 38px; width:100px" id="filter" readonly
                                            value="@lang('common.filter')"> --}}
                                        <input type="button" class="btn btn-info btn-sm"
                                            style="height: 38px; width:100px" id="btnReset" readonly value="Reset">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <div class="col-md-12 row">
                                <div class="pull-left">
                                    <label for="exampleInput" class="" style="margin-left:36px">* List of
                                        employee</label>
                                </div>
                                <div class="col-md-offset-10 "><input class="inputCheckbox" type="checkbox"
                                        id="selectall">
                                    <label for="selectall" class="selectall_lbl">Select All</label>
                                </div>
                            </div>

                        </div>
                        <div>
                            <div id="loading-text" class="col-md-offset-5 hidden">
                                <h3>Loading Please Wait...</h3>
                            </div>
                            <div id="data" class="employee_list"
                                style="overflow:inherit;height:auto;width:auto;border: 2px solid #f1e8e8;padding-bottom:10px">
                                @include('admin.leave.weeklyHoliday.pagination')
                            </div>
                        </div>

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-offset-8 col-md-8">
                                        {{-- @if (isset($editModeData))
                                            <button type="submit" class="btn btn-info btn_style"
                                                style="margin-bottom: 24px"><i class="fa fa-pencil"></i>
                                                @lang('common.update')</button>
                                        @else --}}
                                        <button type="submit" class="btn btn-info btn_style"
                                            style="margin-bottom: 24px"><i class="fa fa-check"></i>
                                            @lang('common.save')</button>
                                        {{-- @endif --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@section('page_scripts')
<script type="text/javascript">
    $(".month").datepicker({
        format: "yyyy-mm",
        minViewMode: "months",
        dateFormat: 'yyyy-mm',
        duration: 'fast',
        todayHighlight: true,
        startDate: new Date(),
    }).on('changeDate', function(e) {
        $(this).datepicker('hide');
    }).focus(function() {
        // $(".datepicker-switch, .prev , .next").remove();
    });

    $('#selectall').click(function() {
        var checked = !$(this).data('checked');
        $('input:checkbox').prop('checked', checked);
        $('.selectall_lbl').html(checked ? 'Unselect All' : 'Select All')
        $(this).data('checked', checked);
    });

    $(document).on('change', '.monthh', function(event) {
        var month = $(this).val();
        $.ajax({
            url: "{{ route('weeklyHoliday.create') }}",
            data: {
                month: month
            },
            success: function(data) {
                $(".employee_list").html(data);
            }
        });
    });

    $(document).on('change', '.day_namee', function(event) {
        var day_name = $(this).val();
        $.ajax({
            url: "{{ route('weeklyHoliday.create') }}",
            data: {
                day_name: day_name
            },
            success: function(data) {
                $(".employee_list").html(data);
            }
        });
    });

    $(document).on('change', '.statuss', function(event) {
        var status = $(this).val();
        var month = $('.month').val();
        $.ajax({
            url: "{{ route('weeklyHoliday.create') }}",
            data: {
                status: status,
                month: month
            },
            success: function(data) {
                $(".employee_list").html(data);
            }
        });
    });

    $(document).on('change', '.department_id', function(event) {
        var department_id = $(this).val();
        $("#designation_id")[0].selectedIndex = 0;
        $('#loading-text').removeClass('hidden');
        $.ajax({
            url: "{{ route('weeklyHoliday.create') }}",
            data: {
                department_id: department_id
            },
            success: function(data) {
                setInterval(() => {
                    $('#loading-text').addClass('hidden');
                }, 500);
                $(".employee_list").html(data);
            }
        });
    });

    $(document).on('change', '.designation_id', function(event) {
        var designation_id = $(this).val();
        $("#department_id")[0].selectedIndex = 0;
        $('#loading-text').removeClass('hidden');
        $.ajax({
            url: "{{ route('weeklyHoliday.create') }}",
            data: {
                designation_id: designation_id
            },
            success: function(data) {
                setInterval(() => {
                    $('#loading-text').addClass('hidden');
                }, 500);
                $(".employee_list").html(data);
            }
        });
    });

    $(document).on('click', '#filterr', function(event) {
        var day_name = $('.day_name').val();
        var month = $('.monthField').val();
        var employee_id = $('.employee_id').val();
        var department_id = $('.department_id').val();

        $('#loading-text').removeClass('hidden');
        $.ajax({
            url: "{{ route('weeklyHoliday.create') }}",
            data: {
                day_name: day_name,
                month: month,
                department_id: department_id,
            },

            success: function(res) {
                console.log(res);
                // $('.employee_list').append(data);
                $('#loading-text').addClass('hidden');
                $(".employee_list").html(data);


            },
            complete: function() {
                $('#loading-text').addClass('hidden');

            }
        });
    });

    $(function() {

        $('.data').on('click', '.pagination a', function(e) {
            getData($(this).attr('href').split('page=')[1]);
            e.preventDefault();

        });
    });

    function getData(page) {
        var department_id = $('.department_id').val();
        var designation_id = $('.designation_id').val();
        var day_name = $('.day_name').val();
        var monthField = $('.monthField').val();
        $.ajax({
            url: '?page=' + page + "&department_id=" + department_id + "&day_name=" + day_name +
                "&designation_id=" + designation_id,
            datatype: "html",
        }).done(function(data) {
            // console.log(data)
            $('.data').html(data);
        }).fail(function(e) {
            console.log(e);
            alert('No response');
        });

    }
</script>

<script type="text/javascript">
    $(function() {
        $("#btnReset").bind("click", function() {
            $("#department_id")[0].selectedIndex = 0;
            $("#designation_id")[0].selectedIndex = 0;
            $("#day_name")[0].selectedIndex = 0;
            $("#status")[0].selectedIndex = 0;

            $('#loading-text').removeClass('hidden');
            $.ajax({
                url: "{{ route('weeklyHoliday.create') }}",
                data: {
                    department_id: '',
                    designation_id: '',
                },
                success: function(data) {
                    $('#loading-text').addClass('hidden');
                    $(".employee_list").html(data);
                },
                complete: function() {
                    $('#loading-text').addClass('hidden');
                }
            });
        });
    });
</script>
@endsection
