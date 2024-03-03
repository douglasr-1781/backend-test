<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RedirectUpdateRequest extends RedirectRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return array_merge_recursive(
            parent::rules(),
            [
                'placeholder' => ['required_without_all:url_to, active'],
            ]            
        );
    }

    public function messages()
    {
        return [
            'required_without_all' => 'Deve ser enviado ao menos um campo para atualização.',
        ];
    }
}
