@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.shift_details')
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
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
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="text-left">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-block alert-dismissable">
                                    <ul>
                                        <button type="button" class="close" data-dismiss="alert">x</button>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if ($message = Session::get('success'))
                                <div class="alert alert-success alert-block">
                                    <button type="button" class="close" data-dismiss="alert">x</button>
                                    <strong>{{ $message }}</strong>
                                </div>
                            @endif
                            @if ($message = Session::get('error'))
                                <div class="alert alert-danger alert-block">
                                    <button type="button" class="close" data-dismiss="alert">x</button>
                                    <strong>{{ $message }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="row col-md-12"
                            style="border: 1px solid #EFEEEF; border-radius:4px;margin:2px;padding:20px 0 0 0;margin-bottom:32px;">
                            <p class="border" style="margin-left:30px">
                                <span><i class="fa fa-upload"></i></span>
                                <span style="margin-left: 4px"><b>Upload Document Here (.xlsx).</b></span>
                            </p>
                            <form class="col-md-8" action="{{ route('shiftDetails.import') }}" method="post"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="col-md-4 text-right">
                                    <input type="file" name="select_file" class="form-control custom-file-upload">
                                </div>
                                <div class="form-group col-md-4">
                                    <div class="">
                                        <input class="col-md-2 form-control monthField"
                                            style="height: 32px;width:100px;background:#fff" required readonly
                                            placeholder="@lang('common.month')" id="month" name="month"
                                            value="@if (isset($month)) {{ $month }}@else {{ date('Y-m') }} @endif">
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-success btn-sm" style="margin-top: 1px;width:90px"
                                            type="submit"><span><i class="fa fa-upload" aria-hidden="true"></i></span>
                                            Upload</button>
                                    </div>
                                </div>
                            </form>
                            <form class="row col-md-4 text-right" action="{{ route('shiftDetails.export') }}"
                                method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <button class="col-md-1 btn btn-info btn-sm pull-right waves-effect waves-light"
                                    type="submit" style="margin-top: 2px;width: 100px;">
                                    <i class="fa fa-download" style="margin-right: 2px;" aria-hidden="true"></i><span>
                                        Template</span>
                                </button>
                                <div class="col-md-2 form-group pull-right" style="width: 120px;">
                                    <input class="form-control monthField" style="height: 32px;background:#fff" required
                                        readonly placeholder="@lang('common.month')" id="month" name="month"
                                        value="@if (isset($month)) {{ $month }}@else {{ date('Y-m') }} @endif">
                                </div>
                            </form>
                        </div>

                        <div class="row">
                            <form class="col-md-10 col-sm-8" action="{{ route('shiftDetails.index') }}" method="post"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="col-md-4 col-sm-1"></div>
                                <label class="col-md-1 control-label" style="padding-top: 6px"
                                    for="email">@lang('common.month')<span class="validateRq">*</span>:</label>
                                <div class="form-group col-sm-4">
                                    <input class="form-control monthField" style="height: 32px;" required readonly
                                        placeholder="@lang('common.month')" id="yearAndMonth" name="yearAndMonth"
                                        value="@if (isset($yearAndMonth)) {{ $yearAndMonth }}@else {{ date('Y-m') }} @endif">
                                </div>
                                <button class="btn btn-info btn-sm col-md-2 waves-effect waves-light" value="Filter"
                                    type="submit" style="margin-top: 1px;margin-right: 10px;width:84px;">
                                    <i class="fa fa-download" aria-hidden="true"></i><span>
                                        {{ 'Filter' }}</span>
                                </button>
                            </form>
                            @if (isset($yearAndMonth))
                                <h4 class="text-right">
                                    <a class="btn btn-success btn-sm pull-right" style="color: #fff;margin-right: 16px"
                                        href="{{ URL('shiftDetails/download/?yearAndMonth=' . $yearAndMonth) }}"><i
                                            class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download')</a>
                                </h4>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered table-striped table-hover"
                                style="font-size: 12px">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>EMP.ID</th>
                                        <th>MONTH</th>
                                        <th>01</th>
                                        <th>02</th>
                                        <th>03</th>
                                        <th>04</th>
                                        <th>05</th>
                                        <th>06</th>
                                        <th>07</th>
                                        <th>08</th>
                                        <th>09</th>
                                        <th>10</th>
                                        <th>11</th>
                                        <th>12</th>
                                        <th>13</th>
                                        <th>14</th>
                                        <th>15</th>
                                        <th>16</th>
                                        <th>17</th>
                                        <th>18</th>
                                        <th>19</th>
                                        <th>20</th>
                                        <th>21</th>
                                        <th>22</th>
                                        <th>23</th>
                                        <th>24</th>
                                        <th>25</th>
                                        <th>26</th>
                                        <th>27</th>
                                        <th>28</th>
                                        <th @if (isset($yearAndMonth) && date('t', strtotime($yearAndMonth . '-01')) < '29') hidden @endif>29</th>
                                        <th @if (isset($yearAndMonth) && date('t', strtotime($yearAndMonth . '-01')) < '30') hidden @endif>30</th>
                                        <th @if (isset($yearAndMonth) && date('t', strtotime($yearAndMonth . '-01')) < '31') hidden @endif>31</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($results as $value)
                                        <tr class="{!! $value->id !!}">
                                            <td style="width: 50px;">{!! ++$sl !!}</td>
                                            <td>{{ $value->finger_print_id }}</td>
                                            <td>{{ $value->month }}</td>
                                            <td>{{ $value->d_1 ? $shift[$value->d_1] : 'NA' }}</td>
                                            <td>{{ $value->d_2 ? $shift[$value->d_2] : 'NA' }}</td>
                                            <td>{{ $value->d_3 ? $shift[$value->d_3] : 'NA' }}</td>
                                            <td>{{ $value->d_4 ? $shift[$value->d_4] : 'NA' }}</td>
                                            <td>{{ $value->d_5 ? $shift[$value->d_5] : 'NA' }}</td>
                                            <td>{{ $value->d_6 ? $shift[$value->d_6] : 'NA' }}</td>
                                            <td>{{ $value->d_7 ? $shift[$value->d_7] : 'NA' }}</td>
                                            <td>{{ $value->d_8 ? $shift[$value->d_8] : 'NA' }}</td>
                                            <td>{{ $value->d_9 ? $shift[$value->d_9] : 'NA' }}</td>
                                            <td>{{ $value->d_10 ? $shift[$value->d_10] : 'NA' }}</td>
                                            <td>{{ $value->d_11 ? $shift[$value->d_11] : 'NA' }}</td>
                                            <td>{{ $value->d_12 ? $shift[$value->d_12] : 'NA' }}</td>
                                            <td>{{ $value->d_13 ? $shift[$value->d_13] : 'NA' }}</td>
                                            <td>{{ $value->d_14 ? $shift[$value->d_14] : 'NA' }}</td>
                                            <td>{{ $value->d_15 ? $shift[$value->d_15] : 'NA' }}</td>
                                            <td>{{ $value->d_16 ? $shift[$value->d_16] : 'NA' }}</td>
                                            <td>{{ $value->d_17 ? $shift[$value->d_17] : 'NA' }}</td>
                                            <td>{{ $value->d_18 ? $shift[$value->d_18] : 'NA' }}</td>
                                            <td>{{ $value->d_19 ? $shift[$value->d_19] : 'NA' }}</td>
                                            <td>{{ $value->d_20 ? $shift[$value->d_20] : 'NA' }}</td>
                                            <td>{{ $value->d_21 ? $shift[$value->d_21] : 'NA' }}</td>
                                            <td>{{ $value->d_22 ? $shift[$value->d_22] : 'NA' }}</td>
                                            <td>{{ $value->d_23 ? $shift[$value->d_23] : 'NA' }}</td>
                                            <td>{{ $value->d_24 ? $shift[$value->d_24] : 'NA' }}</td>
                                            <td>{{ $value->d_25 ? $shift[$value->d_25] : 'NA' }}</td>
                                            <td>{{ $value->d_26 ? $shift[$value->d_26] : 'NA' }}</td>
                                            <td>{{ $value->d_27 ? $shift[$value->d_27] : 'NA' }}</td>
                                            <td>{{ $value->d_28 ? $shift[$value->d_28] : 'NA' }}</td>
                                            <td @if (isset($yearAndMonth) && date('t', strtotime($yearAndMonth . '-01')) < '29') hidden @endif>
                                                {{ $value->d_29 ? $shift[$value->d_29] : 'NA' }}</td>
                                            <td @if (isset($yearAndMonth) && date('t', strtotime($yearAndMonth . '-01')) < '30') hidden @endif>
                                                {{ $value->d_30 ? $shift[$value->d_30] : 'NA' }}</td>
                                            <td @if (isset($yearAndMonth) && date('t', strtotime($yearAndMonth . '-01')) < '31') hidden @endif>
                                                {{ $value->d_31 ? $shift[$value->d_31] : 'NA' }}</td>
                                        </tr>
                                    @endforeach
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
<script type="text/javascript"></script>
@endsection
