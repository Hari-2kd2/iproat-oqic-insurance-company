@extends('admin.master')
@section('content')
@section('title', 'Requested Application Details')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="#"><i class="fa fa-home"></i> Dashboard</a></li>
                <li>@yield('title')</li>

            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>On Duty Application Details</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">Employee On Duty Application Details</h3>
                                <hr>
                                <div class="form-group">
                                    <div class="text-center" style="margin-top: 5px;font-size: 16px;">
                                        <b>
                                            @if (isset($onDutyApplicationData->employee->first_name))
                                                {{ $onDutyApplicationData->employee->first_name }}
                                            @endif
                                            @if (isset($onDutyApplicationData->employee->last_name))
                                                {{ $onDutyApplicationData->employee->last_name }}
                                            @endif
                                        </b>
                                        <div class="text-center" style="font-size: 18px;"><b>
                                                @if (isset($onDutyApplicationData->employee->designation->designation_name))
                                                    {{ $onDutyApplicationData->employee->designation->designation_name }}
                                                @endif
                                            </b>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-md-6 col-sm-6">Applied On :</label>
                                    <p class="col-md-6 col-sm-6">
                                        @if (isset($onDutyApplicationData->application_date))
                                            {{ dateConvertDBtoForm($onDutyApplicationData->application_date) }}
                                        @endif
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-md-6 col-sm-6 ">Period :</label>
                                    <p class="col-md-6 col-sm-6">
                                        @if (isset($onDutyApplicationData->application_date))
                                            {{ dateConvertDBtoForm($onDutyApplicationData->application_from_date) }}
                                        @endif
                                        {{ ' - ' }}
                                        @if (isset($onDutyApplicationData->application_date))
                                            {{ dateConvertDBtoForm($onDutyApplicationData->application_to_date) }}
                                        @endif
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-md-6 col-sm-6 ">Number of days :</label>
                                    <p class="col-md-6 col-sm-6">
                                        @if (isset($onDutyApplicationData->application_date))
                                            {{ $onDutyApplicationData->number_of_day }}
                                        @endif
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-md-6 col-sm-6">Purpose :</label>
                                    <p class="col-md-6 col-sm-6">
                                        @if (isset($onDutyApplicationData->purpose))
                                            {{ $onDutyApplicationData->purpose }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3 class="box-title">On Duty Approval</h3>
                                <hr>
                                {{ Form::open(['route' => ['requestedOnDutyApplication.update', $onDutyApplicationData->on_duty_id], 'method' => 'PUT', 'files' => 'true', 'id' => 'onDutyApproveOrRejectForm']) }}

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 ">From Date :</label>
                                    <p class="col-sm-8"><input type="text" readonly class="form-control"
                                            value="@if (isset($onDutyApplicationData->application_date)) {{ dateConvertDBtoForm($onDutyApplicationData->application_from_date) }} @endif">
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 ">To Date :</label>
                                    <p class="col-sm-8"><input type="text" readonly class="form-control"
                                            value="@if (isset($onDutyApplicationData->application_to_date)) {{ dateConvertDBtoForm($onDutyApplicationData->application_to_date) }} @endif">
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 ">Number of days :</label>
                                    <p class="col-sm-8"> <input type="text" class="form-control"
                                            value="@if (isset($onDutyApplicationData->application_date)) {{ $onDutyApplicationData->number_of_day }} @endif"
                                            readonly></p>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4">Remarks :</label>
                                    <p class="col-sm-8">
                                        <textarea class="form-control" cols="10" rows="6" name="remarks" required placeholder="Enter remarks....."
                                            value="@if (isset($onDutyApplicationData->remarks)) {{ $onDutyApplicationData->remarks }} @endif"></textarea>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4"></label>
                                    <p class="col-sm-8">
                                        <button type="submit" name="status" class="btn btn-info btn_style"
                                            value="2">Approve</button>
                                        <button type="submit" name="status" class="btn btn-danger btn_style"
                                            value="3"> Reject</button>
                                    </p>
                                </div>
                                {{ Form::close() }}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
