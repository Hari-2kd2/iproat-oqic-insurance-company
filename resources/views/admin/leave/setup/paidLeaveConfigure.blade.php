@extends('admin.master')
@section('content')
@section('title')
    @lang('leave.paid_leave_configuration')
@endsection
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
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @lang('leave.rules_of_paid_leave') </div>
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
                                        <th>@lang('leave.for_year')</th>
                                        <th>No of Days</th>
                                        <th>@lang('common.update')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" class="form-control paid_leave_rule_id"
                                                value="{{ $data->paid_leave_rule_id }}">
                                            <input type="number" class="form-control for_year"
                                                value="{{ $data->for_year }}" readonly placeholder="For Days EX:(3)">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control day_of_paid_leave"
                                                value="{{ $data->day_of_paid_leave }}"
                                                placeholder="For Year EX:(1)">
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-success updatePaidRule">
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



        $("body").on("click", ".updatePaidRule", function() {
            var paid_leave_rule_id = $('.paid_leave_rule_id').val();
            var for_year = $('.for_year').val();
            var day_of_paid_leave = $('.day_of_paid_leave').val();

            var action = "{{ URL::to('paidLeaveConfigure/updatePaidLeaveConfigure') }}";
            $.ajax({
                type: "post",
                url: action,
                data: {
                    'paid_leave_rule_id': paid_leave_rule_id,
                    'for_year': for_year,
                    'day_of_paid_leave': day_of_paid_leave,
                    '_token': $('input[name=_token]').val()
                },
                success: function(data) {
                    if (data == 'success') {
                        $.toast({
                            heading: 'success',
                            text: 'Paid leave rule update successfully!',
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
