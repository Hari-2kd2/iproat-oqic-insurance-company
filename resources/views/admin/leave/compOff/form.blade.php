@extends('admin.master')
@section('content')
@section('title')
    @if (isset($editModeData))
        @lang('comp_off.edit_comp_off')
    @else
        @lang('comp_off.add_comp_off')
    @endif
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>

            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <a href="{{ route('compOff.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> @lang('comp_off.view_comp_off')</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (isset($editModeData))
                            {{ Form::model($editModeData, ['route' => ['compOff.update', $editModeData->comp_off_details_id], 'method' => 'PUT', 'files' => 'true', 'id' => 'compOffFormForm', 'class' => 'form-horizontal']) }}
                        @else
                            {{ Form::open(['route' => 'compOff.store', 'enctype' => 'multipart/form-data', 'id' => 'compOffForm', 'class' => 'form-horizontal']) }}
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
                            {{-- @php
                                dd($employeeList);
                            @endphp --}}
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('comp_off.employee') <span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            {!! Form::select('finger_print_id', $employeeList, old('finger_print_id'), [
                                                'class' => 'form-control finger_print_id select2 required',
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('comp_off.off_date')<span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            {!! Form::text(
                                                'off_date',
                                                isset($editModeData) ? dateConvertDBtoForm($editModeData->off_date) : old('off_date'),
                                                $attributes = [
                                                    'class' => 'form-control required dateField off_date',
                                                    'id' => 'off_date',
                                                    'readonly' => 'readonly',
                                                    'placeholder' => __('comp_off.off_date'),
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('comp_off.working_date') <span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select class="form-control select2 required" value="working_date"
                                                name="working_date" id="working_date">
                                                <option value="" selected disabled hidden>{{ '--- Please Select ---' }}</option>
                                            </select><span class="error_ot_date"
                                                style="margin-top:4px;color:red;font-size:12px;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('comp_off.off_timing') <span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            {!! Form::select('off_timing', $offTimingList, old('off_timing'), [
                                                'class' => 'form-control off_timing select2 required' ]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('comp_off.comment')</label>
                                        <div class="col-md-8">
                                            {!! Form::textarea(
                                                'comment',
                                                old('comment'),
                                                $attributes = [
                                                    'class' => 'form-control comment',
                                                    'id' => 'comment',
                                                    'placeholder' => __('comp_off.comment'),
                                                    'cols' => '30',
                                                    'rows' => '2',
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-offset-4 col-md-8">
                                            @if (isset($editModeData))
                                                <button type="submit" class="btn btn-info btn_style"><i
                                                        class="fa fa-pencil"></i> @lang('common.update')</button>
                                            @else
                                                <button type="submit" class="btn btn-info btn_style"><i
                                                        class="fa fa-check"></i> @lang('common.save')</button>
                                            @endif
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
    $(document).ready(function() {
        var isLoading = false;


        $('.finger_print_id, .off_date, .working_date').change(function(e) {
            e.preventDefault();
            if (!isLoading) {
                data();  
            }

        });

        function data() {
            var finger_print_id = $('.finger_print_id').val();
            var off_date = $('.off_date').val();
            var working_date = $('.working_date').val();

            if (finger_print_id != '' && off_date != '') {

                isLoading = true;

                $.ajax({
                    type: "get",
                    url: "{{ route('compOff.getWorkingtime') }}",
                    data: {
                        finger_print_id: finger_print_id,
                        off_date: off_date,
                        working_date: working_date,
                    },
                    success: function(data) {
                        isLoading = false;
                        $(':input[type="submit"]').prop('disabled', false);
                        $('#working_date').empty();
                        $('.error_ot_date').html('');
                        if ($.type(data) === "string") {
                            $(':input[type="submit"]').prop('disabled', true);
                            $('.error_ot_date').html('Not Eligible to Apply!');
                        } else {
                            $('#working_date').html(
                                '<option value="">--- Please Select ---</option>"');
                            console.log(data);
                            $.each(data, function(key, value) {
                                $('#working_date')
                                    .append($("<option></option>")
                                        .attr("value", key)
                                        .text(value));
                            });
                        }
                    }
                });

            }
        }
    });
</script>
@endsection
