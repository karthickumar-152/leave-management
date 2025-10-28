<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\LeaveRequest;
use Carbon\Carbon;

class LeaveHelper
{
    /**
     * Check if user has any overlapping leaves in the given date range
     *
     * @param int $userId The ID of the user to check
     * @param string $startDate Start date of the leave request (Y-m-d format)
     * @param string $endDate End date of the leave request (Y-m-d format)
     * @param int|null $excludeLeaveId Optional leave request ID to exclude from check (for updates)
     * @return bool Returns true if there are overlapping leaves
     */
    public static function hasOverlappingLeaves(int $userId, string $startDate, string $endDate, ?int $excludeLeaveId = null): bool
    {
        $query = LeaveRequest::where('user_id', $userId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    // Check if any existing leave starts during the new leave period
                    $q->where('start_date', '>=', $startDate)
                      ->where('start_date', '<=', $endDate);
                })->orWhere(function ($q) use ($startDate, $endDate) {
                    // Check if any existing leave ends during the new leave period
                    $q->where('end_date', '>=', $startDate)
                      ->where('end_date', '<=', $endDate);
                })->orWhere(function ($q) use ($startDate, $endDate) {
                    // Check if any existing leave completely encompasses the new leave period
                    $q->where('start_date', '<=', $startDate)
                      ->where('end_date', '>=', $endDate);
                });
            })
            ->where('status', '!=', 'Rejected');

        // Exclude the current leave request if we're updating
        if ($excludeLeaveId) {
            $query->where('id', '!=', $excludeLeaveId);
        }

        return $query->exists();
    }

    /**
     * Check if requesting these days would exceed user's leave balance
     */
    public static function willExceedBalance(int $userId, string $startDate, string $endDate): bool
    {
        $user = User::findOrFail($userId);
        $requestedDays = self::calculateLeaveDays($startDate, $endDate);
        
        return ($user->leave_taken + $requestedDays) > $user->annual_leave_balance;
    }

    /**
     * Calculate number of leave days between two dates (excluding weekends)
     */
    public static function calculateLeaveDays(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $days = 0;

        for ($date = $start; $date->lte($end); $date->addDay()) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if (!in_array($date->dayOfWeek, [0, 6])) {
                $days++;
            }
        }

        return $days;
    }

    /**
     * Get user's remaining leave balance
     */
    public static function getRemainingBalance(int $userId): int
    {
        $user = User::findOrFail($userId);
        return $user->annual_leave_balance - $user->leave_taken;
    }

    /**
     * Update user's leave taken count
     */
    public static function updateLeaveTaken(int $userId): void
    {
        $user = User::findOrFail($userId);
        
        // Calculate total approved leave days
        $takenDays = LeaveRequest::where('user_id', $userId)
            ->where('status', 'Approved')
            ->get()
            ->sum(function ($leave) {
                return self::calculateLeaveDays($leave->start_date, $leave->end_date);
            });

        $user->update(['leave_taken' => $takenDays]);
    }
}