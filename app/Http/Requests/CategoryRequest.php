<?php

namespace App\Http\Requests;

use App\Rules\Filter;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name',
                // Custom rule by closure function
                /* function($attribute , $value , $fail){
                   if (stripos($value,'god') !== false)
                       $fail('You cannot use "god" word in your input ');
                }*/

                // Custom rule by Rule class
                // new Filter(['php', 'laravel', 'css'])

                // Custom rule by its define in AppServiceProvider
                'filter:php,laravel,css',
            ],
            'parent_id' => 'nullable|int|exists:categories,id',
            'description' => 'nullable|min:5',
            'status' => 'required|in:active,draft',
            'image' => 'image|max:512000|dimensions:min_width=300,min_height=300'
        ];
    }
}
