<div class="table-responsive" id="devicelog">
    <table id="myTable" class="table table-bordered table-striped ">
        <thead>
            <tr class="tr_header">
                <th>@lang('common.serial')</th>
                <!-- <th>@lang('employee.device_name')</th> -->
                <th>@lang('employee.device_index')</th>
                <th>@lang('attendance.device_type')</th>
                <th>@lang('employee.name')</th>
                <th>@lang('employee.finger_print_no')</th>
                <th>@lang('employee.logtime')</th>
                <!-- <th style="text-align: center;">@lang('common.action')</th> -->
            </tr>
        </thead>
        <tbody>
            {!! $sl = null !!}
            @if (count($results) > 0)
            @foreach ($results as $key => $value)
                @php
                // dd($results);
                    $device = \App\Model\Device::where('id', $value->device)->first();
                    $device = $device ? $device->name : '';
                    $employee = \App\Model\Employee::where('employee_id', $value->employee)->first();
                @endphp

                <tr class="{!! $value->primary_id !!}">
                    <td style="width: 50px;">{!! $results->firstItem() + $key !!}</td>
                    <!-- <td>{!! $value->ID !!}</td> -->
                    <td>{!! $device !!}</td>
                    <td>{!! $value->type !!}</td>
                    <td>{!! $employee->username->user_name !!}</td>
                    <td>{!! $employee->finger_id !!}</td>
                    <td>{{ $value->datetime }}</td>
                    <!-- @if ($value->device_status == 'online')
                    <td class="text-left">
                        <span class="label label-success">Online</span>
                        </td>
                    @else
                    <td class="text-left">
                        <span class="label label-warning">Offline</span>
                        </td>
                    @endif -->
                                        <!-- 	@if ($value->status == 1)
                    <td class="text-left">
                        <span class="label label-success">Active</span>
                        </td>
                    @else
                    <td class="text-left">
                        <span class="label label-warning">In Active</span>
                        </td>
                    @endif -->
                    {{-- <td>{!! $value->created_at !!}</td>
				<td>{!! $value->updated_by !!}</td> --}}
                    <!-- 	<td style="width: 100px;">
                <a href="{!! route('deviceConfigure.edit', $value->id) !!}"
                class="btn btn-success btn-xs btnColor">
                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                </a>
                <a href="{!! route('deviceConfigure.delete', $value->id) !!}"
                data-token="{!! csrf_token() !!}" data-id="{!! $value->id !!}"
                class="delete btn btn-danger btn-xs deleteBtn btnColor"><i
                class="fa fa-trash-o" aria-hidden="true"></i></a>
                </td> -->
                </tr>
            @endforeach
            @endif

        </tbody>
    </table>

    <div class="text-center">

        {{ $results->links() }}

    </div>
</div>
