<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Lib\Enumerations\AppConstant;
use App\Repositories\CommonRepository;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
    }

    public function index()
    {
        $allUsers = User::with('role')->orderBy('user_id', 'desc')->get();
        return view('admin.user.user.index', ['data' => $allUsers]);
    }

    public function create()
    {
        $roleList = $this->commonRepository->roleList();
        return view('admin.user.user.add_user', ['data' => $roleList]);
    }

    public function store(UserRequest $request)
    {

        unset($request['password_confirmation']);
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['created_by'] = Auth::user()->user_id;
        $input['updated_by'] = Auth::user()->user_id;

        try {
            User::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect('user')->with('success', 'User successfully saved.');
        } else {
            return redirect('user')->with('error', 'Something error found !, Please try again.');
        }
    }

    public function edit($id)
    {
        $roleList = $this->commonRepository->roleList();
        $editModeData = User::FindOrFail($id);
        return view('admin.user.user.edit_user', ['data' => $roleList, 'editModeData' => $editModeData]);
    }

    public function update(UserRequest $request, $id)
    {

        $data = User::FindOrFail($id);
        $input = $request->all();
        $input['created_by'] = Auth::user()->user_id;
        $input['updated_by'] = Auth::user()->user_id;

        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'User successfully updated.');
        } else {
            return redirect()->back()->with('error', 'Something error found !, Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::FindOrFail($id);
            $user->delete();
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }

    public function search(Request $request)
    {
        $menus = DB::table('menus')->where('status', AppConstant::$OKEY)
            ->join('menu_permission', 'menu_permission.menu_id', 'menus.id')
            ->where('menu_permission.role_id', (session('logged_session_data.role_id')))
            // ->orWhere('menus.menu_url', 'LIKE', '%' . $request->search . '%')
            ->where('menus.name', 'LIKE', '%' . $request->search . '%')
            ->where('menus.menu_url', '!=', null)
            ->first();

        if ($menus) {
            return redirect()->route($menus->menu_url);
        } else {
            return redirect()->back()->withErrors('Menu not found ! Try different Keyword..');
        }

    }

}
