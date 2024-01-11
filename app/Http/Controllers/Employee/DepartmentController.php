<?php

namespace App\Http\Controllers\Employee;

use App\Model\Employee;
use App\Model\Department;
use App\Components\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;


class DepartmentController extends Controller
{

    public function index()
    {
        $results = Department::get();
        return view('admin.employee.department.index', ['results' => $results]);
    }


    public function create()
    {
        return view('admin.employee.department.form');
    }


    public function store(DepartmentRequest $request)
    {
        $input = $request->all();
        try {
            $department = Department::create($input);


            $pushStatus =  DB::table('sync_to_live')->first();

            if ($pushStatus->status == 1) {
                //Push to LIVE
	
                $form_data = $request->all();
                $form_data['department_id'] = $department->department_id;
                unset($form_data['_method']);
                unset($form_data['_token']);

                $data_set = [];
                foreach ($form_data as $key => $value) {
                    if ($value)
                        $data_set[$key] = $value;
                    else
                        $data_set[$key] = '';
                }

                $client   = new \GuzzleHttp\Client(['verify' => false]);
                $response = $client->request('POST', Common::liveurl() . "addDepartment", [
                    'form_params' => $data_set
                ]);
		
	      

                // PUSH TO LIVE END
            }

            $bug = 0;
        } catch (\Exception $e) {
	   // dd($e->getMessage());
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect('department')->with('success', 'Department successfully saved.');
        } else {
            return redirect('department')->with('error', 'Something Error Found !, Please try again.');
        }
    }


    public function edit($id)
    {
        $editModeData = Department::findOrFail($id);
        return view('admin.employee.department.form', ['editModeData' => $editModeData]);
    }


    public function update(DepartmentRequest $request, $id)
    {
        $department = Department::findOrFail($id);
        $input = $request->all();
        try {
            $department->update($input);

            $pushStatus =  DB::table('sync_to_live')->first();

            if ($pushStatus->status == 1) {
                //Push to LIVE

                $form_data = $request->all();
                $form_data['department_id'] = $department->department_id;
                unset($form_data['_method']);
                unset($form_data['_token']);

                $data_set = [];
                foreach ($form_data as $key => $value) {
                    if ($value)
                        $data_set[$key] = $value;
                    else
                        $data_set[$key] = '';
                }

                $client   = new \GuzzleHttp\Client(['verify' => false]);
                $response = $client->request('POST', Common::liveurl() . "editDepartment", [
                    'form_params' => $data_set
		    
                ]);

                // PUSH TO LIVE END
            }

            $bug = 0;
        } catch (\Exception $e) {
	   // dd($e);
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'Department successfully updated ');
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }


    public function destroy($id)
    {

        $count = Employee::where('department_id', '=', $id)->count();

        if ($count > 0) {

            return  'hasForeignKey';
        }


        try {
            $department = Department::FindOrFail($id);
            $department->delete();

            $pushStatus =  DB::table('sync_to_live')->first();

            if ($pushStatus->status == 1) {
                //Push to LIVE

                $form_data = [];
                $form_data['id'] = $id;
                unset($form_data['_method']);
                unset($form_data['_token']);

                $data_set = [];
                foreach ($form_data as $key => $value) {
                    if ($value)
                        $data_set[$key] = $value;
                    else
                        $data_set[$key] = '';
                }

                $client   = new \GuzzleHttp\Client(['verify' => false]);
                $response = $client->request('POST', Common::liveurl() . "deleteDepartment", [
                    'form_params' => $data_set
                ]);

                // PUSH TO LIVE END
            }


            $bug = 0;
        } catch (\Exception $e) {
            dd($e);
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }

    public function employee_group(Request $request)
    {

        try {
            $departments = DB::connection('sqlsrv')->table('Departments')->get();

            if ($request->action == 'truncate') {
                DB::table('department')->truncate();
            }

            foreach ($departments as $key => $value) {

                $ifExists = DB::table('department')->where('department_name', $value->DepartmentFName)->first();

                if (!$ifExists) {
                    $form_data = Department::create(['department_name' => $value->DepartmentFName]);
                    $this->pushDeptLive($form_data->department_id, $form_data->department_name);
                    // echo "<br>";  echo "Name : " . " || " . $value->DepartmentFName . $key;  echo "<br>";
                }
            }
            return redirect('department')->with('success', 'Department information sync successfully.');
        } catch (\Throwable $th) {
            return redirect('department')->with('error', 'Something went wrong!');
            //throw $th;
        }
        return redirect('department')->with('success', 'Department information sync successfully.');
    }


    public function pushDeptLive($department_id, $department_name)
    {
        //Push to LIVE
        $form_data = [];
        $form_data['department_name'] = $department_name;
        $form_data['department_id'] = $department_id;

        $data_set = [];
        foreach ($form_data as $key => $value) {
            if ($value)
                $data_set[$key] = $value;
            else
                $data_set[$key] = '';
        }

        $client   = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->request('POST', Common::liveurl() . "addDepartment", [
            'form_params' => $data_set
        ]);

        // PUSH TO LIVE END
    }
}
