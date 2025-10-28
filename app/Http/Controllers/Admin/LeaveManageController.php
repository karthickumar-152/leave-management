<?php

namespace App\Http\Controllers\Admin;

use PDF;
use App\Models\User;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Mail\LeaveStatusMail;
use App\Exports\LeaveReportExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class LeaveManageController extends Controller
{
    /**
     * Display all leave requests with filters.
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->from && $request->to) {
            $query->whereBetween('start_date', [$request->from, $request->to]);
        }

        $leaves = $query->latest()->paginate(10);
        $employees = User::role('employee')->get();

        return view('admin.leaves.index', compact('leaves', 'employees'));
    }

    /**
     * Approve a leave request.
     */
    public function approve(LeaveRequest $leave)
    {
        $leave->update([
            'status' => 'Approved',
            'admin_remarks' => 'Approved by Admin',
        ]);

        // Update leave taken count for the user
        if ($leave->user) {
            $takenDays = LeaveRequest::where('user_id', $leave->user_id)
                ->where('status', 'Approved')
                ->get()
                ->sum(function ($leave) {
                    $start = \Carbon\Carbon::parse($leave->start_date);
                    $end = \Carbon\Carbon::parse($leave->end_date);
                    $days = 0;
                    for ($date = $start; $date->lte($end); $date->addDay()) {
                        if (!in_array($date->dayOfWeek, [0, 6])) { // Skip weekends
                            $days++;
                        }
                    }
                    return $days;
                });

            $leave->user->update(['leave_taken' => $takenDays]);

            if ($leave->user->email) {
                Mail::to($leave->user->email)->send(new LeaveStatusMail($leave));
            }
        }

        return back()->with('success', 'Leave approved and email sent successfully.');
    }

    /**
     * Reject a leave request with remarks.
     */
    public function reject(Request $request, LeaveRequest $leave)
    {
        $request->validate(['admin_remarks' => 'required|string|max:255']);

        $leave->update([
            'status' => 'Rejected', // Capitalized to match enum
            'admin_remarks' => $request->admin_remarks,
        ]);

        if ($leave->user && $leave->user->email) {
            Mail::to($leave->user->email)->send(new LeaveStatusMail($leave));
        }

        return back()->with('success', 'Leave request rejected and notification sent.');
    }

    /**
     * Generate report (view/download PDF or Excel)
     */
    public function report(Request $request)
    {
        $query = LeaveRequest::with('user');
        $employees = \App\Models\User::role('employee')->orderBy('name')->get();

        // Apply filters if they exist
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->from && $request->to) {
            $query->whereBetween('start_date', [$request->from, $request->to]);
        }

        if ($request->leave_type) {
            $query->where('leave_type', $request->leave_type);
        }

        $report = $query->latest()->get();

        if ($request->type === 'pdf') {
            return \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.leaves.report_pdf', compact('report'))
                ->download('leave_report_' . now()->format('Y_m_d') . '.pdf');
        }

        if ($request->type === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\LeaveReportExport($report),
                'leave_report_' . now()->format('Y_m_d') . '.xlsx'
            );
        }

        $filters = [
            'status' => $request->status,
            'user_id' => $request->user_id,
            'from' => $request->from,
            'to' => $request->to,
            'leave_type' => $request->leave_type
        ];

        return view('admin.leaves.report', compact('report', 'employees', 'filters'));
    }
}
