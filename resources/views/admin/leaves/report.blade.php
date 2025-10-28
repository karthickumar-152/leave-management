@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary mb-0">
            <i class="bi bi-file-earmark-bar-graph"></i> Leave Reports
        </h3>
        <div>
            <a href="{{ route('admin.leaves.report', array_merge(request()->all(), ['type' => 'pdf'])) }}" class="btn btn-danger">
                <i class="bi bi-file-pdf"></i> Download PDF
            </a>
            <a href="{{ route('admin.leaves.report', array_merge(request()->all(), ['type' => 'excel'])) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Download Excel
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.leaves.report') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="user_id" class="form-label">Employee</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ $filters['user_id'] == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ $filters['status'] === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $filters['status'] === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $filters['status'] === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="leave_type" class="form-label">Leave Type</label>
                    <select name="leave_type" id="leave_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="annual" {{ $filters['leave_type'] === 'annual' ? 'selected' : '' }}>Annual</option>
                        <option value="sick" {{ $filters['leave_type'] === 'sick' ? 'selected' : '' }}>Sick</option>
                        <option value="unpaid" {{ $filters['leave_type'] === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="from" name="from" value="{{ $filters['from'] }}">
                </div>

                <div class="col-md-2">
                    <label for="to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="to" name="to" value="{{ $filters['to'] }}">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped align-middle mb-0">
                <thead class="bg-light text-center">
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($report as $index => $leave)
                        <tr class="text-center">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $leave->user->name ?? 'N/A' }}</td>
                            <td>{{ ucfirst($leave->leave_type) }}</td>
                            <td>{{ $leave->start_date }}</td>
                            <td>{{ $leave->end_date }}</td>
                            <td>{{ ucfirst($leave->status) }}</td>
                            <td>{{ $leave->admin_remarks ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
