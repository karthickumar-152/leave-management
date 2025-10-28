@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4 text-primary">Admin Dashboard</h2>

    <!-- Action Buttons -->
    <div class="text-center mb-5">
        <a href="{{ route('admin.leaves.index') }}" class="btn btn-primary px-4 py-2 me-2">
            View All Leave Requests
        </a>
        <a href="{{ url('/admin/leaves/report') }}" class="btn btn-success px-4 py-2">
            View Leave Report
        </a>
    </div>

    <div class="row g-4">
        <!-- Total Leaves -->
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6>Total Leaves</h6>
                    <h3>{{ \App\Models\LeaveRequest::count() }}</h3>
                </div>
            </div>
        </div>

        <!-- Approved -->
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6>Approved</h6>
                    <h3 class="text-success">
                        {{ \App\Models\LeaveRequest::where('status','approved')->count() }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6>Pending</h6>
                    <h3 class="text-warning">
                        {{ \App\Models\LeaveRequest::where('status','pending')->count() }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Rejected -->
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6>Rejected</h6>
                    <h3 class="text-danger">
                        {{ \App\Models\LeaveRequest::where('status','rejected')->count() }}
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
