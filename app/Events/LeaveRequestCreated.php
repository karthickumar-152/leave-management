<?php

namespace App\Events;

use App\Models\LeaveRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveRequestCreated
{
    use Dispatchable, SerializesModels;

    public $leave;

    public function __construct(LeaveRequest $leave)
    {
        $this->leave = $leave;
    }
}

