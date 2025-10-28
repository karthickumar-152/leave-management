<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Leave Requests') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($leaves->isEmpty())
                        <p class="text-gray-500">No leave requests found.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Leave Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applied On</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($leaves as $leave)
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $leave->leave_type }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $leave->start_date }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $leave->end_date }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $leave->reason }}</td>
                                        <td class="px-6 py-4">
                                            @if($leave->status === 'Approved')
                                                <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                                    <i class="bi bi-check-circle me-1"></i>Approved
                                                </span>
                                            @elseif($leave->status === 'Rejected')
                                                <span class="px-3 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">
                                                    <i class="bi bi-x-circle me-1"></i>Rejected
                                                </span>
                                            @else
                                                <span class="px-3 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">
                                                    <i class="bi bi-clock me-1"></i>Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $leave->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
