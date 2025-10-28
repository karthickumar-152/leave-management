@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Leave Reports</h3>
        <div>
            <a href="{{ route('admin.reports.export', ['type' => 'pdf'] + request()->all()) }}" class="btn btn-danger">
                <i class="bi bi-file-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('admin.reports.export', ['type' => 'excel'] + request()->all()) }}" class="btn btn-success ms-2">
                <i class="bi bi-file-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Month & Year</label>
                    <input type="month" name="month_year" class="form-control" value="{{ request('month_year', $monthYear->format('Y-m')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Employee</label>
                    <select name="user_id" class="form-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('user_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div>
                        <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Requests</h6>
                    <h2 class="card-text">{{ $statistics['total_requests'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Approved</h6>
                    <h2 class="card-text">{{ $statistics['approved'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">Rejected</h6>
                    <h2 class="card-text">{{ $statistics['rejected'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Pending</h6>
                    <h2 class="card-text">{{ $statistics['pending'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly Summary --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Monthly Summary</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Total Days</th>
                            <th>Approved</th>
                            <th>Rejected</th>
                            <th>Pending</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlySummary as $month => $summary)
                        <tr>
                            <td>{{ $month }}</td>
                            <td>{{ $summary['total_days'] }}</td>
                            <td>{{ $summary['approved'] }}</td>
                            <td>{{ $summary['rejected'] }}</td>
                            <td>{{ $summary['pending'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Leave List --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Detailed Leave List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaves as $leave)
                        <tr>
                            <td>{{ $leave->user->name }}</td>
                            <td>{{ $leave->leave_type }}</td>
                            <td>{{ $leave->start_date }}</td>
                            <td>{{ $leave->end_date }}</td>
                            <td>
                                @php
                                    $days = \Carbon\Carbon::parse($leave->start_date)->diffInDaysFiltered(function ($date) {
                                        return !in_array($date->dayOfWeek, [0, 6]);
                                    }, \Carbon\Carbon::parse($leave->end_date)) + 1;
                                @endphp
                                {{ $days }}
                            </td>
                            <td>
                                <span class="badge bg-{{ $leave->status === 'Approved' ? 'success' : ($leave->status === 'Rejected' ? 'danger' : 'warning') }}">
                                    {{ $leave->status }}
                                </span>
                            </td>
                            <td>{{ $leave->admin_remarks ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection