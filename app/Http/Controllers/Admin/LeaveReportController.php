<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaveReportController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::with('user');
        $monthYear = $request->month_year ? Carbon::createFromFormat('Y-m', $request->month_year) : Carbon::now();
        $employees = User::role('employee')->get();

        // Basic filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Month filter
        if ($request->month_year) {
            $query->whereMonth('start_date', $monthYear->month)
                  ->whereYear('start_date', $monthYear->year);
        }

        // Department filter (if you add departments later)
        if ($request->department_id) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $leaves = $query->latest()->get();

        // Calculate statistics
        $statistics = [
            'total_requests' => $leaves->count(),
            'approved' => $leaves->where('status', 'Approved')->count(),
            'rejected' => $leaves->where('status', 'Rejected')->count(),
            'pending' => $leaves->where('status', 'Pending')->count(),
            'by_type' => $leaves->groupBy('leave_type')->map->count(),
            'by_employee' => $leaves->groupBy('user.name')->map->count(),
        ];

        // Monthly summary
        $monthlySummary = [];
        foreach ($leaves as $leave) {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            
            $days = 0;
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                if (!in_array($date->dayOfWeek, [0, 6])) { // Skip weekends
                    $days++;
                }
            }
            
            $month = $startDate->format('F Y');
            if (!isset($monthlySummary[$month])) {
                $monthlySummary[$month] = [
                    'total_days' => 0,
                    'approved' => 0,
                    'rejected' => 0,
                    'pending' => 0
                ];
            }
            
            $monthlySummary[$month]['total_days'] += $days;
            $monthlySummary[$month][strtolower($leave->status)]++;
        }

        return view('admin.reports.index', compact(
            'leaves', 
            'employees', 
            'statistics', 
            'monthlySummary',
            'monthYear'
        ));
    }

    public function export(Request $request)
    {
        $query = LeaveRequest::with('user');
        
        if ($request->month_year) {
            $monthYear = Carbon::createFromFormat('Y-m', $request->month_year);
            $query->whereMonth('start_date', $monthYear->month)
                  ->whereYear('start_date', $monthYear->year);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $leaves = $query->latest()->get();

        if ($request->type === 'pdf') {
            $pdf = \PDF::loadView('admin.reports.export_pdf', [
                'leaves' => $leaves,
                'filters' => $request->only(['month_year', 'status', 'user_id']),
            ]);
            return $pdf->download('leave_report_' . now()->format('Y_m_d') . '.pdf');
        }

        return Excel::download(
            new LeaveReportExport($leaves), 
            'leave_report_' . now()->format('Y_m_d') . '.xlsx'
        );
    }
}