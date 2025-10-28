@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-primary mb-0">
                    <i class="bi bi-pencil-square"></i> Edit Leave Request
                </h3>
                <a href="{{ route('employee.leaves.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Leaves
                </a>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('employee.leaves.update', $leave) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="leave_type" class="form-label">Leave Type</label>
                            <select name="leave_type" id="leave_type" class="form-select @error('leave_type') is-invalid @enderror" required>
                                <option value="">Select Leave Type</option>
                                <option value="annual" {{ old('leave_type', $leave->leave_type) === 'annual' ? 'selected' : '' }}>Annual Leave</option>
                                <option value="sick" {{ old('leave_type', $leave->leave_type) === 'sick' ? 'selected' : '' }}>Sick Leave</option>
                                <option value="unpaid" {{ old('leave_type', $leave->leave_type) === 'unpaid' ? 'selected' : '' }}>Unpaid Leave</option>
                            </select>
                            @error('leave_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                        id="start_date" name="start_date" value="{{ old('start_date', $leave->start_date) }}"
                                        min="{{ date('Y-m-d') }}" required>
                                    @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                        id="end_date" name="end_date" value="{{ old('end_date', $leave->end_date) }}"
                                        min="{{ date('Y-m-d') }}" required>
                                    @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Leave</label>
                            <textarea name="reason" id="reason" rows="3"
                                class="form-control @error('reason') is-invalid @enderror"
                                required>{{ old('reason', $leave->reason) }}</textarea>
                            @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Leave Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection