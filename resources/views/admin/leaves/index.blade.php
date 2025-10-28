@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Manage Leave Requests</h3>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.leaves.index') }}" class="mb-4 row">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- All Status --</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="user_id" class="form-select">
                <option value="">-- All Employees --</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('user_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
        </div>
        <div class="col-md-12 mt-2">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.leaves.index') }}" class="btn btn-secondary">Reset</a>
            <a href="{{ route('admin.leaves.report', ['type' => 'pdf']) }}" class="btn btn-danger float-end ms-2">Export PDF</a>
            <a href="{{ route('admin.leaves.report', ['type' => 'excel']) }}" class="btn btn-success float-end">Export Excel</a>
        </div>
    </form>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Employee</th>
                    <th>Leave Type</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $index => $leave)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $leave->user->name ?? '-' }}</td>
                        <td>{{ ucfirst($leave->leave_type) }}</td>
                        <td>{{ $leave->start_date }}</td>
                        <td>{{ $leave->end_date }}</td>
                        <td>{{ $leave->reason }}</td>
                        <td>
                            @php
                                $status = strtolower($leave->status);
                                // For debugging - will show in HTML comments
                                echo "<!-- Status value: " . $status . " -->";
                            @endphp
                            @if($status === 'pending')
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock me-1"></i>Pending
                                </span>
                            @elseif($status === 'approved')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Approved
                                </span>
                            @elseif($status === 'rejected')
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i>Rejected
                                </span>
                            @else
                                <!-- Unknown status -->
                                <span class="badge bg-secondary">
                                    {{ ucfirst($status) }}
                                </span>
                            @endif
                        </td>
                        <td>{{ $leave->admin_remarks ?? '-' }}</td>
                        <td>
                            @php
                                // Debug status value
                                echo "<!-- Current status: '" . $leave->status . "' -->";
                            @endphp
                            @if($leave->status === 'Pending')
                                <form action="{{ route('admin.leaves.approve', $leave->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-circle me-1"></i> Approve
                                    </button>
                                </form>

                                <!-- Reject Button with Modal -->
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $leave->id }}">
                                    <i class="bi bi-x-circle me-1"></i> Reject
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="rejectModal{{ $leave->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $leave->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('admin.leaves.reject', $leave->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reject Leave</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label for="admin_remarks" class="form-label">Remarks</label>
                                                    <textarea name="admin_remarks" class="form-control" required></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-x-circle me-1"></i> Confirm Reject
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted fst-italic">No actions available</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No leave requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $leaves->links() }}
</div>
@endsection
