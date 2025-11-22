<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatebookRequest extends FormRequest
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
        $bookId = $this->route('book') ?? $this->route('id');
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'auther_id' => 'required|exists:authers,id',
            'publisher_id' => 'required|exists:publishers,id',
            'isbn' => 'nullable|string|unique:books,isbn,' . $bookId,
            'edition' => 'nullable|string|max:50',
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'total_quantity' => 'nullable|integer|min:1',
        ];
    }
}
