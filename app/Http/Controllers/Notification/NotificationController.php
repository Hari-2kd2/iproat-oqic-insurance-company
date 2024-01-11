<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Notifications\DashboardNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markNotification(Request $request)
    {
        auth()->user()
            ->unreadNotifications
            ->when($request->input('id'), function ($query) use ($request) {
                return $query->where('id', $request->input('id'));
            })
            ->markAsRead();
        return redirect('dashboard');
        // return response()->noContent();
    }

    public function notifications()
    {
        return auth()->user()->unreadNotifications()->limit(10)->get()->toArray();
        // return 'test';
    }

    public function sendNotifications()
    {
        $user = auth()->user();
        $user->notify(new DashboardNotification(auth()->user()));
        return redirect('dashboard');
        // return 'test';
    }
}
