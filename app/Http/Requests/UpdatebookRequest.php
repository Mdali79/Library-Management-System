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
            'publisher_id' => 'required|exists:publishers,id',
            'isbn' => 'nullable|string|unique:books,isbn,' . $bookId,
            'edition' => 'nullable|string|max:50',
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf_file' => 'nullable|file|mimes:pdf|max:51200', // Max 50MB
            'preview_pages' => 'nullable|integer|min:1|max:500',
            'total_quantity' => 'nullable|integer|min:1',
            'authors' => 'required|array|min:1',
            'authors.*.id' => 'required|exists:authers,id',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $authors = $this->input('authors', []);

            if (empty($authors)) {
                $validator->errors()->add('authors', 'At least one author is required.');
                return;
            }

            // Check for duplicate authors
            $authorIds = array_column($authors, 'id');
            $authorIds = array_filter($authorIds); // Remove empty values
            if (count($authorIds) !== count(array_unique($authorIds))) {
                $validator->errors()->add('authors', 'Duplicate authors are not allowed.');
            }
        });
    }
}
