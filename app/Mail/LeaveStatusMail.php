<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;

    public function __construct(LeaveRequest $leave)
    {
        $this->leave = $leave;
    }

    public function build()
    {
        return $this->subject('Leave Request Update')
                    ->view('emails.leave_status')
                    ->with([
                        'employee' => $this->leave->user->name,
                        'status' => ucfirst($this->leave->status),
                        'from' => $this->leave->start_date,
                        'to' => $this->leave->end_date,
                    ]);
    }
}

