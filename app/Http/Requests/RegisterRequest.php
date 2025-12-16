<?php
// app/Http/Requests/RegisterRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:customer,driver',
            'address' => 'nullable|string|max:500',
            'postcode' => 'nullable|string|max:20',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Please provide a valid email',
            'email.unique' => 'This email is already registered',
            'phone.required' => 'Phone number is required',
            'phone.unique' => 'This phone number is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'role.required' => 'Please select a role',
            'role.in' => 'Invalid role selected',
        ];
    }
}