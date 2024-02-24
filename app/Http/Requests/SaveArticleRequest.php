<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SaveArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data.attributes.title' => ['required', 'min:4'],
            'data.attributes.slug' => ['required', 'alpha_dash', Rule::unique('articles', 'slug')->ignore($this->route('article'))],
            'data.attributes.content' => ['required'],
        ];
    }

    /**
     * Get the validated data from the request.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return array
     */
    public function validated($key = null, $default = null): array
    {
        return parent::validated($key, $default)['data']['attributes'];
    }
}
