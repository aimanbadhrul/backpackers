<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'event_id' => 'required|exists:events,id',
            'user_id' => 'required|exists:users,id',
            // 'status' => 'nullable|in:pending,approved,rejected,confirmed',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'submission_date' => 'nullable|date',
    
            // Optional
            'notes' => 'nullable|string',
            'approval_date' => 'nullable|date',
            'rejection_reason' => 'nullable|string',
            'payment_status' => 'nullable|in:pending,paid,waived',
            'payment_receipt' => 'nullable|file',
            'group' => 'nullable|string',
            'special_requests' => 'nullable|string',
        ];

        if (backpack_auth()->user()->hasRole('Superadmin') || 
        (isset($this->application) && 
         $this->application->event->created_by == backpack_auth()->id())) {
        $rules['status'] = 'required|in:pending,approved,rejected,confirmed';
    }

    return $rules;
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
