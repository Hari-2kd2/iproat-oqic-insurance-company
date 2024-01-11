<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\AccessControl;
use App\Model\Device;
use App\Model\MsSql;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{

    public function add(Request $request)
    {

        DB::beginTransaction();
        $device = Device::create($request->all());
        Device::where('id', $device->id)->update(['id' => $request->id]);
        DB::commit();

        return json_encode(['status' => 'success', 'message' => 'Device created Successfully !'], 200);

    }

    public function update(Request $request)
    {

        DB::beginTransaction();
        $device = Device::findOrFail($request->id);
        $device->update($request->all());
        DB::commit();

        return json_encode(['status' => 'success', 'message' => 'Device Successfully updated !'], 200);

    }

    public function importlogs(Request $request)
    {
        \set_time_limit(0);
        Log::info($request->all());

        try {
            DB::beginTransaction();
            $device = new MsSql;
            $device->local_primary_id = $request->primary_id;
            $device->ID = $request->ID;
            $device->type = $request->type;
            $device->datetime = $request->datetime;
            $device->status = $request->status;
            $device->device_name = $request->device_name;
            $device->created_at = now();
            $device->updated_at = now();
            $device->save();
            DB::commit();
            return json_encode(['status' => 'success', 'message' => 'Device Log Successfully updated !'], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            info($th->getMessage());
            return json_encode(['status' => 'failed', 'message' => 'Device Log failed to update !'], 200);
        }

        

    }

    public function destroy(Request $request)
    {

        $devices = Device::FindOrFail($request->id);
        $devices->status = 2;
        $devices->save();

        AccessControl::where('device', $request->id)->delete();

        return json_encode(['status' => 'success', 'message' => 'Device Log Successfully updated !'], 200);
    }

}
