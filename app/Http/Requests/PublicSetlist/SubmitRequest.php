<?php

namespace App\Http\Requests\PublicSetlist;

use Illuminate\Foundation\Http\FormRequest;

class SubmitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public access is allowed
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'items' => 'required|array',
            'items.*.type' => 'required|in:song,break',
            'items.*.song_id' => 'required_if:items.*.type,song|exists:songs,id|nullable',
            'items.*.title' => 'required_if:items.*.type,break|string|nullable',
            'items.*.duration_seconds' => 'required|integer',
            'items.*.notes' => 'nullable|string',
            'items.*.order' => 'required|integer|min:0'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.*.type.required' => 'Each item must have a type (song or break)',
            'items.*.type.in' => 'Item type must be either song or break',
            'items.*.song_id.required_if' => 'Song items must have a song ID',
            'items.*.song_id.exists' => 'Selected song does not exist',
            'items.*.title.required_if' => 'Break items must have a title',
            'items.*.duration_seconds.required' => 'Each item must have a duration',
            'items.*.order.required' => 'Each item must have an order position',
        ];
    }
} 