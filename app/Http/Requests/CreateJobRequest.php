<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateJobRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'service_id' => 'required|exists:services,id',
            'pickup_address' => 'required|string|max:500',
            'pickup_postcode' => 'required|string|max:20',
            'pickup_latitude' => 'nullable|numeric|between:-90,90',
            'pickup_longitude' => 'nullable|numeric|between:-180,180',
            'delivery_address' => 'nullable|string|max:500',
            'delivery_postcode' => 'nullable|string|max:20',
            'delivery_latitude' => 'nullable|numeric|between:-90,90',
            'delivery_longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string|max:1000',
            'scheduled_at' => 'nullable|date|after:now',
        ];
    }
}