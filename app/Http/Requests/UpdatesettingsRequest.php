<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatesettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'return_days' => 'required|integer|min:1|max:365',
            'fine_per_day' => 'required|numeric|min:0',
            'fine_grace_period_days' => 'required|integer|min:0|max:30',
            'max_borrowing_limit_student' => 'required|integer|min:1|max:50',
            'max_borrowing_limit_teacher' => 'required|integer|min:1|max:50',
            'max_borrowing_limit_librarian' => 'required|integer|min:1|max:50',
        ];
    }
}
