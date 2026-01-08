<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorebookRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'publisher_id' => 'required|exists:publishers,id',
            'isbn' => 'nullable|string|unique:books,isbn',
            'edition' => 'nullable|string|max:50',
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'total_quantity' => 'nullable|integer|min:1',
            'authors' => 'required|array|min:1',
            'authors.*.id' => 'required|exists:authers,id',
            'authors.*.is_main' => 'nullable|boolean',
            'authors.*.is_corresponding' => 'nullable|boolean',
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
            $mainAuthorIndex = $this->input('main_author');
            
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

            // Ensure at least one main author - check both is_main field and main_author radio
            $hasMainAuthor = false;
            
            // First check if main_author radio button was submitted
            if (!empty($mainAuthorIndex) && isset($authors[$mainAuthorIndex]) && !empty($authors[$mainAuthorIndex]['id'])) {
                $hasMainAuthor = true;
            } else {
                // Fallback: check is_main field in authors array
                foreach ($authors as $author) {
                    if (isset($author['is_main']) && (!empty($author['is_main']) || $author['is_main'] === '1' || $author['is_main'] === 1 || $author['is_main'] === true)) {
                        $hasMainAuthor = true;
                        break;
                    }
                }
            }
            
            if (!$hasMainAuthor) {
                $validator->errors()->add('authors', 'At least one author must be marked as Main Author.');
            }

            // Ensure maximum one corresponding author
            $correspondingCount = 0;
            foreach ($authors as $author) {
                if (isset($author['is_corresponding']) && (!empty($author['is_corresponding']) || $author['is_corresponding'] === '1' || $author['is_corresponding'] === 1)) {
                    $correspondingCount++;
                }
            }
            
            if ($correspondingCount > 1) {
                $validator->errors()->add('authors', 'Only one author can be marked as Corresponding Author.');
            }
        });
    }
}
