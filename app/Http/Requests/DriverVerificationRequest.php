<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DriverVerificationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

        public function rules()
        {
            return [
                'driving_license_front' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
                'driving_license_back'  => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
                'vehicle_photo'         => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'number_plate'          => 'required|string|max:20',
                'waste_carrier_license' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
                'vehicle_type'          => 'required|string|max:100',
            ];
        }

        public function messages()
        {
            return [
                'driving_license_front.required' => 'Driving license front image is required',
                'driving_license_back.required'  => 'Driving license back image is required',
                'vehicle_photo.required'         => 'Vehicle photo is required',
                'number_plate.required'          => 'Number plate is required',
                'waste_carrier_license.required' => 'Waste carrier license is required',
                'vehicle_type.required'          => 'Vehicle type is required',
            ];
        }


}