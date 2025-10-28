<?php

namespace App\Http\Controllers\Employee;

use App\Helpers\LeaveHelper;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Events\LeaveRequestCreated;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreLeaveRequest;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = LeaveRequest::where('user_id', Auth::id())->latest()->get();
        return view('employee.leave.index', compact('leaves'));
    }

    public function create()
    {
        return view('employee.leave.create');
    }

    public function store(StoreLeaveRequest $request)
    {
        // Check for overlapping leaves
        if (LeaveHelper::hasOverlappingLeaves(Auth::id(), $request->start_date, $request->end_date)) {
            return back()->withErrors(['overlap' => 'You already have a leave during these dates.']);
        }

        // Check leave balance
        if (LeaveHelper::willExceedBalance(Auth::id(), $request->start_date, $request->end_date)) {
            return back()->withErrors(['balance' => 'Insufficient leave balance for this duration.']);
        }

        $leave = LeaveRequest::create([
            'user_id' => Auth::id(),
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
        ]);

        // Fire event for email notification to admin
        event(new LeaveRequestCreated($leave));

        // Optional log confirmation
        Log::info('LeaveRequestCreated event fired for leave ID: ' . $leave->id);

        return redirect()->route('employee.leaves.index')->with('success', 'Leave request submitted successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        // Use LeaveRequest model
        $leave = LeaveRequest::findOrFail($id);

        $leave->status = $request->status;
        $leave->admin_remarks = $request->status === 'Rejected' ? 'Rejected by admin' : 'Approved by admin';
        $leave->save();

        // Send email to employee
        Mail::to($leave->user->email)->send(new \App\Mail\LeaveStatusMail($leave));

        return redirect()->back()->with('success', 'Leave status updated and email sent.');
    }

}
