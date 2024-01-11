@extends('admin.master')
@section('content')
@section('title')
    Mobile Attendance
@endsection
<style>
    .navbar .navbar-header .menuIcon {
        position: relative;
    }

    .navbar .navbar-header .imageIcon {
        position: relative;
        /* bottom: -12; */
    }

    /* .navbar .navbar-header .imageDropdown {
        margin-top: -11;
    } */

    .navbar .navbar-header .menuIcon {
        background: none;
        display: initial;
    }

    .navbar .navbar-header .imageIcon {
        background: none;
        display: initial;
    }

    .mapouter {
        position: relative;
        text-align: center;
        width: 300px;
        height: 100px;
    }

    .gmap_canvas {
        overflow: hidden;
        background: none !important;
        width: 300px;
        height: 100px;
        text-align: center;
    }

    .gmap_iframe {
        width: 300px !important;
        height: 100px !important;
        text-align: center;
    }

    .map_canvas {
        height: 100%;
        width: 100%;
        margin: 0px;
        padding: 0px;
        text-align: center;
    }

    table.dataTable thead th,
    table.dataTable thead td {
        padding: 10px 18px;
        border-bottom: 1px solid #e4e7ea;
    }

    .table {
        font-size: 13px;
    }

    th,
    tr td {
        text-align: center;
        vertical-align: middle;
    }
</style>

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
        <div class="col-md-12">
            <div class="panel panel-info">
                @php
                    $name = App\Model\Employee::where('employee_id', $_REQUEST['employee_id'])
                        ->select('first_name', 'last_name')
                        ->first();
                @endphp
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')
                    @if (isset($name))
                        {{ '( ' . $name->first_name . ' ' . $name->last_name . ' - ' . $_REQUEST['date'] . ' )' }}
                    @else
                        {{ '( ' . $_REQUEST['date'] . ' )' }}
                    @endif
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr class="tr_header">
                                        <th class="text-center">Face Check Image</th>
                                        <th class="text-center">Employee Name</th>
                                        <th class="text-center">Attendance</th>
                                        <th class="text-center">Location</th>
                                        {{-- <th class="text-center">Distance</th> --}}
                                    </tr>
                                </thead>
                                @php
                                    $distance_travelled = 0;
                                @endphp
                                @forelse ($employeeInfo as $key=> $value)
                                    <tbody class="text-center">
                                        @if (count($employeeInfo) > 0)
                                            <tr class="text-center">
                                                <td>
                                                    @if (isset($value['face_id_in']) && $value['face_id_in'] != '')
                                                        <a href="{{ asset('public/storage/faceId/' . $value['face_id_in']) }}"
                                                            target="_blank">
                                                            <img id="face_image"
                                                                src="{{ asset('public/storage/faceId/' . $value['face_id_in']) }}"
                                                                style="width:120px;height:80px;object-fit: cover"
                                                                alt="Image" class="img-square">
                                                        </a>
                                                    @else
                                                        <img style="width:120px;height:80px;object-fit: fill"
                                                            src="{!! asset('admin_assets/img/default.png') !!}" alt="user-img"
                                                            class="img-square">
                                                    @endif
                                                    <hr>
                                                    @if (isset($value['face_id_out']) && $value['face_id_out'] != '')
                                                        <a href="{{ asset('public/storage/faceId/' . $value['face_id_out']) }}"
                                                            target="_blank"><img
                                                                style="width:120px;height:80px;object-fit: cover"
                                                                src="{{ asset('public/storage/faceId/' . $value['face_id_out']) }}"
                                                                alt="user-img" class="img-square"></a>
                                                    @else
                                                        <img style="width:120px;height:80px;object-fit: fill"
                                                            src="{!! asset('admin_assets/img/default.png') !!}" alt="user-img"
                                                            class="img-square">
                                                    @endif
                                                </td>

                                                <td class="text-center" style="vertical-align: middle">
                                                    {{ 'Employee ID:' }}
                                                    {{ $value['finger_id'] }}<br>
                                                    <hr>
                                                    {{ 'Name:' }} {{ $value['first_name'] }}<br>
                                                    <hr>
                                                    {{ 'Department:' }}{{ $value['department_name'] }}<br>
                                                    <hr>
                                                    {{ 'Designation:' }}{{ $value['designation_name'] }}
                                                </td>

                                                <td class="text-center" style="vertical-align: middle">
                                                    @if (isset($value['check_in']) && isset($value['in_time']))
                                                        <b>{{ 'Check ' . $value['check_in'] . ' @ ' . date('d/m/Y h:i A', strtotime($value['in_time'])) }}</b><br>
                                                        {{ $value['in_address'] }}
                                                    @elseif (isset($value['in_time']))
                                                        {{ date('d/m/Y H:i', strtotime($value['in_time'])) . ' : ' . 'Web' }}
                                                    @else
                                                        {{ '--' }}
                                                    @endif
                                                    <hr>
                                                    @if (isset($value['check_out']) && isset($value['out_time']))
                                                        <b>{{ 'Check ' . $value['check_out'] . ' @ ' . date('d/m/Y h:i A', strtotime($value['out_time'])) }}</b>
                                                        <br>
                                                        {{ $value['out_address'] }}
                                                    @elseif (isset($value['out_time']))
                                                        {{ date('d/m/Y H:i', strtotime($value['out_time'])) . ' : ' . 'Web' }}
                                                    @else
                                                        {{ '--' }}
                                                    @endif
                                                </td>

                                                <td class="text-center">
                                                    @if (isset($value['check_in']) && isset($value['lat_in']) && isset($value['lng_in']))
                                                        <div class="">
                                                            <div class="">
                                                                <iframe height="80" frameborder="0" style="border:0"
                                                                    src="https://www.google.com/maps/embed/v1/place?key={{ config('services.googlekey.ApiKey') }}&q={{ $value['lat_in'] }},{{ $value['lng_in'] }}">
                                                                </iframe>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <img class="" style="height: 100px"
                                                            src="{!! asset('admin_assets/img/404.png') !!}">
                                                    @endif
                                                    <hr>
                                                    @if (isset($value['check_in']) && isset($value['lat_out']) && isset($value['lng_out']))
                                                        <div class="">
                                                            <div class="">
                                                                <iframe height="80" frameborder="0" style="border:0"
                                                                    src="https://www.google.com/maps/embed/v1/place?key={{ config('services.googlekey.ApiKey') }}&q={{ $value['lat_in'] }},{{ $value['lng_in'] }}">
                                                                </iframe>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <img class="" style="height: 100px"
                                                            src="{!! asset('admin_assets/img/404.png') !!}">
                                                    @endif
                                                </td>

                                                {{-- @if (isset($value['distance']))
                                                    @php
                                                        $distance_travelled += (float) number_format((float) $value['distance'], 6, '.', '');
                                                    @endphp
                                                    <td class="text-center"
                                                        style="padding-top: 52px;padding-bottom:52px">
                                                        {{ number_format((float) $value['distance'], 2, '.', '') . ' ' . 'Km' }}
                                                    </td>
                                                @else
                                                    <td class="text-center"
                                                        style="padding-top: 52px;padding-bottom:52px">
                                                        {{ '0.00 Km' }}</td>
                                                @endif --}}


                                            </tr>
                                        @endif
                                    </tbody>
                                @empty
                                    <tr>
                                        <td colspan="10" id="view">No Data Found</td>
                                    </tr>
                                @endforelse
                                {{-- @if (count($employeeInfo) > 0)
                                    <tr class="text-right bg-title">
                                        <td colspan="9" style="font-size: 16px; font-weight:400;">
                                            {{ 'Total distance travelled :' }}</td>
                                        <td colspan="1" class="text-center"
                                            style="font-size: 14px; font-weight:400;">
                                            {{ number_format((float) $distance_travelled, 2, '.', '') . ' ' . 'Km' }}
                                        </td>
                                    </tr>
                                @endif --}}
                            </table>

                        </div>
                        <div style="margin:12px 0 12px 0;font-weight:bold">Map View</div>
                        <div id="dvMap"></div>
                        <!-- Replace the value of the key parameter with your own API key. -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style type="text/css">
    #dvMap {
        height: 100%;
    }

    /* Optional: Makes the sample page fill the window. */

    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    iframe {
        border: none;
    }
</style>
@endsection

@php

    $mapData = [];
    $count = count($employeeInfo);

    foreach ($employeeInfo as $key => $data) {
        $mapData[] = [
            'name' => $data['first_name'],
            'lat' => $data['lat_in'],
            'lng' => $data['lng_in'],
            'address' => $data['in_address'],
            'face_id' => $data['face_id_in'],
        ];
    }

@endphp

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googlekey.ApiKey') }}&sensor=true"
    type="text/javascript"></script>

@section('page_scripts')
<script>
    const img = document.getElementById("face_image")
    img.addEventListener("error", function(event) {
        event.target.src = "{{ asset('admin_assets/img/default.png') }}"
        event.onerror = null
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        init();
    });

    /// Fresh route path
    var markers = <?php echo $map_dataset; ?>;
    // var markers = <?= $map_dataset ?>

    function init() {

        var mapOptions = {
            center: new google.maps.LatLng(markers[0].latitude, markers[0].longitude),
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
        var infoWindow = new google.maps.InfoWindow();
        var lat_lng = new Array();
        var latlngbounds = new google.maps.LatLngBounds();
        for (i = 0; i < markers.length; i++) {
            var data = markers[i]
            var myLatlng = new google.maps.LatLng(data.latitude, data.longitude);
            lat_lng.push(myLatlng);
            var marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
                title: data.timestamp
            });
            // console.log(i)

            latlngbounds.extend(marker.position);
            (function(marker, data) {
                google.maps.event.addListener(marker, "click", function(e) {
                    infoWindow.setContent(data.timestamp);
                    infoWindow.open(map, marker);
                });
            })(marker, data);
        }
        map.setCenter(latlngbounds.getCenter());
        map.fitBounds(latlngbounds);

        //***********ROUTING****************//
        //Initialize the Direction Service
        var service = new google.maps.DirectionsService();

        //Loop and Draw Path Route between the Points on MAP
        for (var i = 0; i < lat_lng.length; i++) {
            if ((i + 1) < lat_lng.length) {
                var src = lat_lng[i];
                var des = lat_lng[i + 1];
                // path.push(src);

                service.route({
                    origin: src,
                    destination: des,
                    travelMode: google.maps.DirectionsTravelMode.WALKING
                }, function(result, status) {
                    if (status == google.maps.DirectionsStatus.OK) {

                        //Initialize the Path Array
                        var path = new google.maps.MVCArray();
                        //Set the Path Stroke Color
                        var poly = new google.maps.Polyline({
                            map: map,
                            strokeColor: '#4986E7'
                        });
                        poly.setPath(path);
                        for (var i = 0, len = result.routes[0].overview_path.length; i < len; i++) {
                            path.push(result.routes[0].overview_path[i]);
                        }
                    }
                });
            }
        }
    }
</script>
@endsection
