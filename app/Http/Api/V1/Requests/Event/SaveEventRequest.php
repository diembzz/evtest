<?php

namespace App\Http\Api\V1\Requests\Event;

use App\Rules\ImageSizeMin;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributes(): array
    {
        return [
            'poster.src' => 'poster'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'event_date' => 'required|date',
            'venue_id' => 'required|int|exists:venues,id',
            'poster.src' => ['required', new ImageSizeMin(400, 400)],
        ];
    }
}
