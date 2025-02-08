<?php

namespace App\Http\Requests\Band;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Any authenticated user can create a band
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cover_image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,gif',
                'max:10240', // 10MB
                'dimensions:min_width=640,min_height=360,max_width=2000,max_height=1125',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $image = getimagesize($value->path());
                        if ($image) {
                            $width = $image[0];
                            $height = $image[1];
                            $ratio = $width / $height;
                            
                            // Allow aspect ratios between 1.7 and 1.78 (close to 16:9 = 1.77778)
                            if ($ratio < 1.7 || $ratio > 1.78) {
                                $fail('The image must have an aspect ratio of 16:9 (like 1920x1080).');
                            }
                        }
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cover_image.max' => 'The cover image must not be larger than 10MB.',
            'cover_image.dimensions' => 'The cover image must be between 640x360 and 2000x1125 pixels with a 16:9 aspect ratio.',
            'cover_image.mimes' => 'The cover image must be a file of type: jpeg, png, gif.',
        ];
    }
}
