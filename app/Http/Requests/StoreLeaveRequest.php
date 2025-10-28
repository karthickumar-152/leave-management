<?php

namespace App\Http\Requests;

use App\Helpers\LeaveHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasRole('employee');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'leave_type' => 'required|string|max:50',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:255',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check for overlapping leaves
            if (LeaveHelper::hasOverlappingLeaves(
                Auth::id(),
                $this->start_date,
                $this->end_date
            )) {
                $validator->errors()->add('overlap', 'You already have a leave during these dates.');
            }

            // Check leave balance
            if (LeaveHelper::willExceedBalance(
                Auth::id(),
                $this->start_date,
                $this->end_date
            )) {
                $validator->errors()->add(
                    'balance',
                    'Insufficient leave balance. You have ' . 
                    LeaveHelper::getRemainingBalance(Auth::id()) . 
                    ' days remaining.'
                );
            }
        });
    }
}