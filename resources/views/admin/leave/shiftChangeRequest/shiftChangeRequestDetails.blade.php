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
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>Shift Change Application Details
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">Employee Shift Change Application Details</h3>
                                <hr>
                                <div class="text-left" style="">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-md-6 col-sm-6">Designation :</label>
                                        <p class="col-md-6 col-sm-6">
                                            @if (isset($shiftChangeRequestData->employee->designation->designation_name))
                                            {{ $shiftChangeRequestData->employee->designation->designation_name }}
                                        @endif
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-md-6 col-sm-6">Full Name :</label>
                                        <p class="col-md-6 col-sm-6">
                                            @if (isset($shiftChangeRequestData->employee->first_name))
                                                {{ $shiftChangeRequestData->employee->first_name }}
                                            @endif
                                            @if (isset($shiftChangeRequestData->employee->last_name))
                                                {{ $shiftChangeRequestData->employee->last_name }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-md-6 col-sm-6">Applied On :</label>
                                        <p class="col-md-6 col-sm-6">
                                            @if (isset($shiftChangeRequestData->application_date))
                                                {{ dateConvertDBtoForm($shiftChangeRequestData->application_date) }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-md-6 col-sm-6 ">Period :</label>
                                        <p class="col-md-6 col-sm-6">
                                            @if (isset($shiftChangeRequestData->application_date))
                                                {{ dateConvertDBtoForm($shiftChangeRequestData->application_from_date) }}
                                            @endif
                                            {{ ' - ' }}
                                            @if (isset($shiftChangeRequestData->application_date))
                                                {{ dateConvertDBtoForm($shiftChangeRequestData->application_to_date) }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-md-6 col-sm-6">Regular Shift :</label>
                                        <p class="col-md-6 col-sm-6">
                                            @if (isset($shiftChangeRequestData->regular_shift))
                                                {{ shiftList($shiftChangeRequestData->regular_shift) }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-md-6 col-sm-6">Requested Shift :</label>
                                        <p class="col-md-6 col-sm-6">
                                            @if (isset($shiftChangeRequestData->work_shift_id))
                                                {{ shiftList($shiftChangeRequestData->work_shift_id) }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-md-6 col-sm-6">Purpose :</label>
                                        <p class="col-md-6 col-sm-6">
                                            @if (isset($shiftChangeRequestData->purpose))
                                                {{ $shiftChangeRequestData->purpose }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3 class="box-title">Shift Change Approval</h3>
                                <hr>
                                {{ Form::open(['route' => ['requestedShiftChangeRequest.update', $shiftChangeRequestData->shift_change_request_id], 'method' => 'PUT', 'files' => 'true', 'id' => 'requestedShiftChangeRequestForm']) }}
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4">Remarks :</label>
                                    <p class="col-sm-8">
                                        <textarea class="form-control" cols="10" rows="6" name="remarks" required placeholder="Enter remarks....."
                                            value="@if (isset($shiftChangeRequestData->remarks)) {{ $shiftChangeRequestData->remarks }} @endif"></textarea>
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
