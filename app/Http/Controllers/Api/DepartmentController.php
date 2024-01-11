<?php

namespace App\Http\Controllers\Api;

use App\User;
use Exception;
use App\Model\Employee;
use App\Model\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\EmployeeRepository;

class DepartmentController extends Controller{


    public function add(Request $request){
        
        DB::beginTransaction();
        $department=Department::create($request->all());
        Department::where('department_id',$department->department_id)->update(['department_id'=>$request->department_id]);
        DB::commit();

        return json_encode(['status'=>'success','message'=>'Employee created Successfully !'],200);

    }

    public function update(Request $request){
        
        DB::beginTransaction();
        $department=Department::findOrFail($request->department_id);
        $department->update($request->all());
        DB::commit();

        return json_encode(['status'=>'success','message'=>'Successfully updated !'],200);

    }

    public function destroy(Request $request){
        
        try {
            $department = Department::FindOrFail($request->id);
            $department->delete();
            return json_encode(['status'=>'success','message'=>'Successfully updated !'],200);
        }catch(Exception $e) {
            return json_encode(['status'=>'error','message'=>$e->getMessage()],400);
        }
        
         
    }

  
}