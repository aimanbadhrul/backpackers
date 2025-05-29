<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    
     public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->id,
            'roles' => 'required',
        ];

        if ($this->isMethod('post')) {
            // On create
            $rules['password'] = 'required|min:6|confirmed';
        } elseif ($this->filled('password')) {
            // On update, only if password is being changed
            $rules['password'] = 'min:6|confirmed';
        }

        return $rules;
    }
}
