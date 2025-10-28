<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employee Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-2">Welcome, {{ Auth::user()->name }} 👋</h3>
                    <p class="text-gray-600 mb-4">Use the options below to manage your leave requests.</p>

                    <!-- Leave Balance Card -->
                    @php
                        $takenDays = App\Models\LeaveRequest::where('user_id', Auth::id())
                            ->where('status', 'Approved')
                            ->get()
                            ->sum(function ($leave) {
                                $start = \Carbon\Carbon::parse($leave->start_date);
                                $end = \Carbon\Carbon::parse($leave->end_date);
                                $days = 0;
                                for ($date = $start; $date->lte($end); $date->addDay()) {
                                    if (!in_array($date->dayOfWeek, [0, 6])) {
                                        $days++;
                                    }
                                }
                                return $days;
                            });
                        $remainingDays = Auth::user()->annual_leave_balance - $takenDays;
                    @endphp
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-600 font-medium">Annual Leave Balance</p>
                                <p class="text-2xl font-bold text-blue-700">{{ Auth::user()->annual_leave_balance }} days</p>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <p class="text-sm text-green-600 font-medium">Remaining Balance</p>
                                <p class="text-2xl font-bold text-green-700">{{ $remainingDays }} days</p>
                                <p class="text-xs text-green-500 mt-1">Available to use</p>
                            </div>
                            <div class="text-center p-4 bg-amber-50 rounded-lg">
                                <p class="text-sm text-amber-600 font-medium">Leave Taken</p>
                                <p class="text-2xl font-bold text-amber-700">{{ $takenDays }} days</p>
                                <p class="text-xs text-amber-500 mt-1">Used this year</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Leaves -->
                    @php
                        $pendingLeaves = App\Models\LeaveRequest::where('user_id', Auth::id())
                            ->where('status', 'Pending')
                            ->latest()
                            ->get();
                    @endphp
                    @if($pendingLeaves->count() > 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <h4 class="text-yellow-800 font-medium mb-2">Pending Leave Requests</h4>
                        <ul class="space-y-2">
                            @foreach($pendingLeaves as $leave)
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-yellow-700">{{ $leave->leave_type }}: {{ $leave->start_date }} to {{ $leave->end_date }}</span>
                                <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded text-xs">Awaiting Approval</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                        <a href="{{ route('employee.leaves.create') }}" class="block p-6 bg-indigo-100 hover:bg-indigo-200 text-indigo-800 rounded-xl shadow-md transition">
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-semibold">📝 Apply for Leave</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m2 4H7m2-8h6m2-4H7m5-2a2 2 0 00-2 2h4a2 2 0 00-2-2z" />
                                </svg>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">Submit a new leave request for admin approval.</p>
                        </a>

                        <a href="{{ route('employee.leaves.index') }}" class="block p-6 bg-green-100 hover:bg-green-200 text-green-800 rounded-xl shadow-md transition">
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-semibold">📄 View My Leave Requests</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 8h10M7 12h4m-2 8a9 9 0 110-18 9 9 0 010 18z" />
                                </svg>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">Check the status of your applied leaves.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
