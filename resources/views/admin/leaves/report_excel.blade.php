<table>
    <thead>
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
        @foreach($report as $i => $leave)
            <tr>
                <td>{{ $i + 1 }}</td>
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
