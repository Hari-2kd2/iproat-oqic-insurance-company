@extends('admin.master')
@section('content')
@section('title')
    @lang('shift_change.shift_change_application_form')
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
            <a href="{{ route('applyForShiftChange.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> @lang('shift_change.view_shift_change_application')</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@lang('shift_change.shift_change_form')</div>
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

                        {{ Form::open(['route' => 'applyForShiftChange.store', 'enctype' => 'multipart/form-data', 'id' => 'shiftChangeApplicationForm']) }}
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
                                    <div class="form-group">
                                        <label>@lang('shift_change.regular_shift')<span class="validateRq">*</span></label>
                                        {!! Form::select('regular_shift', $shiftList, old('regular_shift'), [
                                            'class' => 'form-control regular_shift select2 required',
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('shift_change.work_shift')<span class="validateRq">*</span></label>
                                        {!! Form::select('work_shift_id', $shiftList, old('work_shift_id'), [
                                            'class' => 'form-control work_shift_id select2 required',
                                        ]) !!}
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
                                                'class' => 'form-control application_from_date dateField',
                                                'readonly' => 'readonly',
                                                'placeholder' => __('common.from_date'),
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="exampleInput">@lang('common.to_date')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        {!! Form::text(
                                            'application_to_date',
                                            old('application_to_date'),
                                            $attributes = [
                                                'class' => 'form-control application_to_date dateField',
                                                'readonly' => 'readonly',
                                                'placeholder' => __('common.to_date'),
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('shift_change.purpose')<span
                                                class="validateRq">*</span></label>
                                        {!! Form::textarea(
                                            'purpose',
                                            old('purpose'),
                                            $attributes = [
                                                'class' => 'form-control purpose',
                                                'id' => 'purpose',
                                                'placeholder' => __('common.purpose'),
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
                                            class="fa fa-paper-plane"></i> @lang('common.send_application')</button>
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



        $(document).on("change", ".regular_shift, .work_shift_id", function() {
            var regular_shift = $('.regular_shift').val();
            var new_shift = $('.work_shift_id').val();

            if (regular_shift == new_shift) {
                $('body').find('#formSubmit').attr('disabled', true);
            } else {
                $('body').find('#formSubmit').attr('disabled', false);
            }

        });

    });
</script>
@endsection
