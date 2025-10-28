<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\LeaveRequestCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLeaveRequestEmail
{
    public function handle(LeaveRequestCreated $event)
    {
        $leave = $event->leave;

        //Fetch all admin users from Spatie roles
        $adminUsers = User::role('admin')->get();

        if ($adminUsers->isEmpty()) {
            Log::warning('No admin users found to send leave request email.');
            return;
        }

        foreach ($adminUsers as $admin) {
            Mail::raw("New leave request from {$leave->user->name} ({$leave->leave_type})", function ($message) use ($admin) {
                $message->to($admin->email)->subject('New Leave Request Submitted');
            });

            Log::info("Leave request email sent to admin: {$admin->email}");
        }
    }
}

