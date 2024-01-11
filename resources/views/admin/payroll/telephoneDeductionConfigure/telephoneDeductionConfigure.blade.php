@extends('admin.master')
@section('content')
@section('title')
    @lang('leave.earn_leave_configuration')
@endsection
{{-- @php
dd($data);
@endphp --}}
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
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
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @lang('leave.rules_of_earn_leave') </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
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
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Cost Per Call</th>
                                        <th>Limit Per Month</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                        <th>@lang('common.update')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden"
                                                class="form-control telephone_allowance_deduction_rule_id"
                                                value="{{ $data->telephone_allowance_deduction_rule_id }}">
                                            <input type="number" class="form-control cost_per_call"
                                                value="{{ $data->cost_per_call }}" placeholder="Cost Per Phone Call">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control limit_per_month"
                                                value="{{ $data->limit_per_month }}" placeholder="Limit Per Month">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control status"
                                                value="{{ $data->status }}" placeholder="Status" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control remarks"
                                                value="{{ $data->remarks }}" placeholder="Remarks">
                                        </td>

                                        <td>
                                            <button type="button"
                                                class="btn btn-sm btn-success updateTelephoneDeductionRule">
                                                @lang('common.update')
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
    jQuery(function() {
        $("body").on("click", ".updateTelephoneDeductionRule", function() {
            var telephone_allowance_deduction_rule_id = $('.telephone_allowance_deduction_rule_id')
                .val();
            var cost_per_call = $('.cost_per_call').val();
            var limit_per_month = $('.limit_per_month').val();
            var status = $('.status').val();
            var remarks = $('.remarks').val();
            var action = "{{ URL::to('telephoneDeductionConfigure/updateTelephoneDeductionConfigure') }}";
            $.ajax({
                type: 'post',
                url: action,
                data: {
                    'telephone_allowance_deduction_rule_id': telephone_allowance_deduction_rule_id,
                    'cost_per_call': cost_per_call,
                    'limit_per_month': limit_per_month,
                    'status': status,
                    'remarks': remarks,
                    '_token': $('input[name=_token]').val()
                },
                success: function(data) {
                    if (data == 'success') {
                        $.toast({
                            heading: 'success',
                            text: 'Telephone Deduction rule update successfully!',
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'success',
                            hideAfter: 3000,
                            stack: 6
                        });
                    } else {
                        $.toast({
                            heading: 'Problem',
                            text: 'Something error found !',
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'error',
                            hideAfter: 3000,
                            stack: 6
                        });
                    }

                }
            });
        })
    });
</script>
@endsection
