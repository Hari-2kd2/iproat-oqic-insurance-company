@extends('admin.master')
@section('content')
@section('title')
    @lang('onduty.onduty_application_form')
@endsection
<style>
    .datepicker table tr td.disabled,
    .datepicker table tr td.disabled:hover {
        background: none;
        color: red !important;
        cursor: default;
    }

    td {
        color: black !important;
    }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="#"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>

            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <a href="{{ route('applyForOnDuty.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> @lang('onduty.view_on_duty')</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@lang('onduty.on_duty_form')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">×</span></button>
                                @foreach ($errors->all() as $error)
                                    <strong>{!! $error !!}</strong><br>
                                @endforeach
                            </div>
                        @endif
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        {{ Form::open(['route' => 'applyForOnDuty.store', 'enctype' => 'multipart/form-data', 'id' => 'OnDutyApplicationForm']) }}
                        <div class="form-body">
                            <div class="row">
                                {!! Form::hidden(
                                    'employee_id',
                                    isset($getEmployeeInfo) ? $getEmployeeInfo->employee_id : '',
                                    $attributes = ['class' => 'employee_id'],
                                ) !!}
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('common.employee_name')<span
                                                class="validateRq">*</span></label>
                                        {!! Form::text(
                                            '',
                                            isset($getEmployeeInfo) ? $getEmployeeInfo->first_name . ' ' . $getEmployeeInfo->last_name : '',
                                            $attributes = ['class' => 'form-control', 'readonly' => 'readonly'],
                                        ) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInput">@lang('common.from_date')<span class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        {!! Form::text(
                                            'application_from_date',
                                            old('application_from_date'),
                                            $attributes = [
                                                'class' => 'form-control application_from_date',
                                                'readonly' => 'readonly',
                                                'placeholder' => __('common.from_date'),
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInput">@lang('common.to_date')<span class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        {!! Form::text(
                                            'application_to_date',
                                            old('application_to_date'),
                                            $attributes = [
                                                'class' => 'form-control application_to_date',
                                                'readonly' => 'readonly',
                                                'placeholder' => __('common.to_date'),
                                            ],
                                        ) !!}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('leave.number_of_day')<span
                                                class="validateRq">*</span></label>
                                        {!! Form::text(
                                            'number_of_day',
                                            '',
                                            $attributes = [
                                                'class' => 'form-control number_of_day',
                                                'readonly' => 'readonly',
                                                'placeholder' => __('leave.number_of_day'),
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                            
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('leave.purpose')<span
                                                class="validateRq">*</span></label>
                                        {!! Form::textarea(
                                            'purpose',
                                            old('purpose'),
                                            $attributes = [
                                                'class' => 'form-control purpose',
                                                'id' => 'purpose',
                                                'placeholder' => __('leave.purpose'),
                                                'cols' => '30',
                                                'rows' => '3',
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" id="formSubmit" class="btn btn-info "><i
                                            class="fa fa-paper-plane"></i> @lang('leave.send_application')</button>
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
<script>
    jQuery(function() {

        $(document).on("focus", ".application_from_date", function() {
            $(this).datepicker({
                format: 'dd/mm/yyyy',
                todayHighlight: true,
                clearBtn: true,
                startDate: new Date(),
            }).on('changeDate', function(e) {
                $(this).datepicker('hide');
            });
        });

        $(document).on("focus", ".application_to_date", function() {
            $(this).datepicker({
                format: 'dd/mm/yyyy',
                todayHighlight: true,
                clearBtn: true,
                startDate: new Date(),
            }).on('changeDate', function(e) {
                $(this).datepicker('hide');
            });
        });

        $(document).on("change", ".application_from_date,.application_to_date  ", function() {
            var application_from_date = $('.application_from_date ').val();
            var application_to_date = $('.application_to_date ').val();

            if (application_from_date != '' && application_to_date != '') {
                var action = "{{ URL::to('applyForLeave/applyForTotalNumberOfDays') }}";
                $.ajax({
                    type: 'POST',
                    url: action,
                    data: {
                        'application_from_date': application_from_date,
                        'application_to_date': application_to_date,
                        '_token': $('input[name=_token]').val()
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('.number_of_day').val(data);
                        $('body').find('#formSubmit').attr('disabled', false);
                    }
                });
            } else {
                $('body').find('#formSubmit').attr('disabled', true);
            }
        });
    });
</script>
@endsection
