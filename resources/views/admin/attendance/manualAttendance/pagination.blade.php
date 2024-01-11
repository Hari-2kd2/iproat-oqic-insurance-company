<div class="table-responsive">

    <table id="myTable" class="table table-bordered">

        <thead class="tr_header">
            <tr>
                {{-- <th style="width:60px;">@lang('common.serial')</th> --}}
                {{-- <th>Date</th> --}}
                <th>Name</th>
                <th>Employee Id</th>
                <th>@lang('attendance.in_time')</th>
                <th>@lang('attendance.out_time')</th>
                <th>@lang('attendance.remarks')</th>
                <th>@lang('attendance.updated_by')</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
           
            @foreach ($results as $key => $value)
                <tr class="{{ $value->finger_print_id }}">
                    {{-- <td style="vertical-align:center;">
                        {{ 1 + $key }}</td> --}}
                    {{-- <td style="vertical-align:center;">
                        {{ $_REQUEST['date'] }}</td> --}}
                    <td style="vertical-align:center;">
                        {{ ucwords(trim($value->employee->first_name . ' ' . $value->employee->last_name)) }}
                    </td>
                    <td style="vertical-align:center;">
                        {{ $value->finger_print_id }}
                    </td>
                    <td style="vertical-align:center;">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input class="form-control datetime in_time intime{{ $value->finger_print_id }}"
                                id="in_time" type="text" placeholder="@lang('attendance.in_time')" name="in_time"
                                data-id="{{ $value->finger_print_id }}" value="{{ $value->in_time }}">
                        </div>
                    </td>

                    <td style="vertical-align:center;">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input class="form-control datetime out_time outtime{{ $value->finger_print_id }}"
                                id="out_time" type="text" placeholder="@lang('attendance.out_time')" name="out_time"
                                data-id="{{ $value->finger_print_id }}" value="{{ $value->out_time }}">
                        </div>
                    </td>

                    <td style="vertical-align:center;">
                        <div class="form-group">
                            <textarea class="form-control col-md-2 remarks{{ $value->finger_print_id }}" id="remarks" type="text"
                                placeholder="@lang('attendance.remarks')" name="remarks" data-id="{{ $value->finger_print_id }}" rows="1"> {{ isset($value->remarks) ? $value->remarks : null }} </textarea>
                        </div>
                    </td>

                    <td style="vertical-align:center;">
                        @if (isset($value->updatedBy) && $value->updatedBy != null)
                            {{ ucwords(trim($value->updatedBy->first_name . ' ' . $value->updatedBy->last_name)) }}
                            <br>
                            {{ '@ ' . date('Y-m-d h:i A', strtotime($value->updated_at)) }}
                        @else
                            {{ 'NA @' }} <br>
                            {{ '0000-00-00 00:00:00' }}
                        @endif
                    </td>

                    <td style="vertical-align:center;" class="text-center">
                        @if (count($results) > 0)
                            <a type="submit" href="{!! route('manualAttendance.individualReport', [
                                'finger_print_id' => $value->finger_print_id,
                            ]) !!}" data-token="{!! csrf_token() !!}"
                                data-id="{!! $value->finger_id !!}" class="generateReportIndividually">
                                <button class="btn btn-info btn-sm" title="save" id="rptSave"><i
                                        class="fa fa-save"></i></button></a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
