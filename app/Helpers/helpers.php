<?php

use App\Lib\Enumerations\AppConstant;
use App\Model\Branch;
use App\Model\FrontSetting;
use App\Model\ManualAttendance;
use App\Model\MsSql;
use App\Model\WorkShift;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

function shiftList($id = null)
{
    $workShift = DB::table('work_shift')->get();
    $result = ['' => '---- Please Select ----'];

    foreach ($workShift as $key => $value) {
        $result[$value->work_shift_id] = $value->shift_name;
    }

    if ($id) {
        return $result[$id];
    }

    return $result;
}

function multipleBranchList()
{
    $branches = DB::table('branch')->get();
    $result = ['' => '--- All branch ---'];
    foreach ($branches as $value) {
        $result[$value->branch_id] = $value->branch_name;
    }
    return $result;
}

function branchList()
{
    $branches = DB::table('branch')->get();
    $result = ['' => '---- Please Select ----'];

    foreach ($branches as $value) {
        $result[$value->branch_id] = $value->branch_name;
    }
    return $result;
}

function fullOrHalfDay($status)
{
    $array = array("0" => 'HalfDay', "1" => 'FullDay');
    foreach ($array as $key => $value) {
        if ((int) $key == $status) {
            return $value;
        }
    }
}

function dateConvertFormtoDB($date)
{
    if (!empty($date)) {
        return date("Y-m-d", strtotime(str_replace('/', '-', $date)));
    }
}

function monthConvertFormtoDB($month)
{
    if (!empty($month)) {
        return date("Y-m", strtotime(str_replace('/', '-', $month)));
    }
}

function weekOffDateList($day, $month)
{
    // $start_date = $month . '-01';
    // $end_date   = date("Y-m-t", strtotime($start_date));

    $date = new DateTime('first ' . $day . ' of this month');
    // $thisMonth = $date->format('m');
    $thisMonth = date('m', strtotime($month));
    $dates = array();
    $i = 0;
    while ($date->format('m') === $thisMonth) {
        $i++;
        $dates[] .= $date->format('Y-m-d');
        $date->modify('next ' . $day);
    }
    return $dates;
}

function monthConvertDBtoForm($month)
{
    if (!empty($month)) {
        $month = strtotime($month);
        return date('Y/m', $month);
    }
}
function dateConvertDBtoForm($date)
{
    if (!empty($date)) {
        $date = strtotime($date);
        return date('d/m/Y', $date);
    }
}
function findMonthFromToDate($start_date, $end_date)
{

    $target = strtotime($start_date);

    $workingDate = [];

    while ($target <= strtotime(date("Y-m-d", strtotime($end_date)))) {
        $temp = [];
        $temp['date'] = date('Y-m-d', $target);
        $temp['day'] = date('d', $target);
        $temp['day_name'] = date('D', $target);
        $workingDate[] = $temp;
        $target += (60 * 60 * 24);
    }
    // dd($workingDate);
    return $workingDate;
}

function employeeInfo()
{
    return DB::select("call SP_getEmployeeInfo('" . (session('logged_session_data.employee_id')) . "')");
}

function permissionCheck()
{

    $role_id = (session('logged_session_data.role_id'));
    return $result = json_decode(DB::table('menus')->select('menu_url')
        ->join('menu_permission', 'menu_permission.menu_id', '=', 'menus.id')
        ->where('menu_permission.role_id', '=', $role_id)
        ->whereNotNull('action')->get()->toJson(), true);
}

function showMenu()
{
    $role_id = (session('logged_session_data.role_id'));
    $modules = json_decode(DB::table('modules')->get()->toJson(), true);
    $menus = json_decode(DB::table('menus')
        ->select(DB::raw('menus.id, menus.name, menus.menu_url, menus.parent_id, menus.module_id'))
        ->join('menu_permission', 'menu_permission.menu_id', '=', 'menus.id')
        ->where('menu_permission.role_id', $role_id)
        ->where('menus.status', 1)
        ->whereNull('action')
        ->orderBy('menus.id', 'ASC')
        ->get()->toJson(), true);

    $sideMenu = [];
    if ($menus) {
        foreach ($menus as $menu) {
            if (!isset($sideMenu[$menu['module_id']])) {
                $moduleId = array_search($menu['module_id'], array_column($modules, 'id'));

                $sideMenu[$menu['module_id']] = [];
                $sideMenu[$menu['module_id']]['id'] = $modules[$moduleId]['id'];
                $sideMenu[$menu['module_id']]['name'] = $modules[$moduleId]['name'];
                $sideMenu[$menu['module_id']]['icon_class'] = $modules[$moduleId]['icon_class'];
                $sideMenu[$menu['module_id']]['menu_url'] = '#';
                $sideMenu[$menu['module_id']]['parent_id'] = '';
                $sideMenu[$menu['module_id']]['module_id'] = $modules[$moduleId]['id'];
                $sideMenu[$menu['module_id']]['sub_menu'] = [];
            }
            if ($menu['parent_id'] == 0) {
                $sideMenu[$menu['module_id']]['sub_menu'][$menu['id']] = $menu;
                $sideMenu[$menu['module_id']]['sub_menu'][$menu['id']]['sub_menu'] = [];
            } else {
                array_push($sideMenu[$menu['module_id']]['sub_menu'][$menu['parent_id']]['sub_menu'], $menu);
            }
        }
    }

    return $sideMenu;
}

function convartMonthAndYearToWord($data)
{
    $monthAndYear = explode('-', $data);

    $month = $monthAndYear[1];
    $dateObj = DateTime::createFromFormat('!m', $month);
    $monthName = $dateObj->format('F');
    $year = $monthAndYear[0];

    return $monthAndYearName = $monthName . " " . $year;
}

function employeeAward()
{
    return ['Employee of the Month' => 'Employee of the Month', 'Employee of the Year' => 'Employee of the Year', 'Best Employee' => 'Best Employee'];
}

function weekedName()
{
    $week = array("Sun" => 'Sunday', "Mon" => 'Monday', "Tue" => 'Tuesday', "Wed" => 'Wednesday', "Thu" => 'Thursday', "Fri" => 'Friday', "Sat" => 'Saturday');
    return $week;
}

function attStatus($att_status)
{
    $status = array("1" => 'Present', "2" => 'Absent', "3" => 'Leave', "4" => 'Holiday', "5" => 'Future', "6" => 'Update', "7" => 'Error', "8" => 'Missing OUT Punch', "9" => 'Missing In Punch', '10' => 'Less Hours', '11' => 'Comp Off');
    foreach ($status as $key => $value) {
        if ((int) $key == $att_status) {
            return $value;
        }
    }
}

function userStatus($att_status)
{
    $status = array("0" => 'Probation Period', "1" => 'Active', "2" => 'Inactive', "3" => 'Terminated', "4" => 'Permanent');
    foreach ($status as $key => $value) {
        if ((int) $key == $att_status) {
            return $value;
        }
    }
}

function allDevices()
{
    $options = [];
    $device = MsSql::select('device_name')->groupBy('device_name')->get('device_name')->toArray();
    $manual = ManualAttendance::select('device_name')->groupBy('device_name')->get('device_name')->toArray();
    $devices = (object) array_merge($device, $manual);

    foreach ($devices as $value) {
        $options[] = $value['device_name'] != null ? $value['device_name'] : "N/A";
    }

    return $options;
}

function findMonthToAllDate($month)
{
    $start_date = $month . '-01';
    $end_date = date("Y-m-t", strtotime($start_date));

    $target = strtotime($start_date);
    $workingDate = [];
    while ($target <= strtotime(date("Y-m-d", strtotime($end_date)))) {
        $temp = [];
        $temp['date'] = date('Y-m-d', $target);
        $temp['day'] = date('d', $target);
        $temp['day_name'] = date('D', $target);
        $workingDate[] = $temp;
        $target += (60 * 60 * 24);
    }
    return $workingDate;
}

function findFromDateToDateToAllDate($start_date, $end_date)
{
    $target = strtotime($start_date);
    $workingDate = [];
    while ($target <= strtotime(date("Y-m-d", strtotime($end_date)))) {
        $temp = [];
        $temp['date'] = date('Y-m-d', $target);
        $temp['day'] = date('d', $target);
        $temp['day_name'] = date('D', $target);
        $workingDate[] = $temp;
        $target += (60 * 60 * 24);
    }
    return $workingDate;
}

function findMonthToStartDateAndEndDate($month)
{
    $start_date = $month . '-01';
    $end_date = date("Y-m-t", strtotime($start_date));
    $data = [
        'start_date' => $start_date,
        'end_date' => $end_date,
    ];
    return $data;
}

function getFrontData()
{
    $setting = FrontSetting::orderBy('id', 'desc')->first();

    return $setting;
}

function password($count)
{
    $result = "";
    for ($value = 0; $value <= $count; $value++) {
        $result = $result . '*';
    }
    return $result;
}

function getRouteData($search)
{
    $options = [];

    $qry = '1 ';
    if ($search != '') {
        $qry = 'menus.menu_url LIKE  %' . $search . '%';
    }
    $menus = DB::table('menus')->where('status', AppConstant::$OKEY)
        ->where('menus.menu_url', '!=', null)
        ->join('menu_permission', 'menu_permission.menu_id', 'menus.id')
        ->where('menu_permission.role_id', (session('logged_session_data.role_id')))
        ->whereRaw($qry)
        ->orderBy('menus.name')
        ->get();

    foreach ($menus as $value) {
        $options[$value->menu_url] = $value->name;
    }

    return $options;
}

function getModelData()
{
    $options = [
        'App\User',
        'App\Model\Employee',
    ];

    return $options;
}
