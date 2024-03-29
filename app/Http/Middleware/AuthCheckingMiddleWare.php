<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class AuthCheckingMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (null != session('logged_session_data') && (session('logged_session_data.role_id'))) {
            $role_id = (session('logged_session_data.role_id'));
            $namedRoute = Route::currentRouteName();
            $current_url_check = DB::table('menus')->select('menu_url')->where('menu_url', $namedRoute)->where('status', 1)->get()->toArray();
            if ($namedRoute) {
                if ($current_url_check) {
                    $permissionCheck = DB::table('menus')
                        ->join('menu_permission', 'menu_permission.menu_id', '=', 'menus.id')
                        ->where('role_id', $role_id)
                        ->where('menu_url', $namedRoute)
                        ->get()->toArray();
                    if (empty($permissionCheck) || count($permissionCheck) < 0) {
                        return response()->view('errors.404', [], 404);
                    }
                }
            }
        }
        return $next($request);
    }
}
