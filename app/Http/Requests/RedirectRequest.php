<?php

namespace App\Http\Requests;

use App\Rules\ExternalUrl;
use App\Rules\ValidUrl;
use Illuminate\Foundation\Http\FormRequest;

class RedirectRequest extends FormRequest
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
        return [
            'url_to' => ['bail', 'url:https', 'starts_with:https', new ValidUrl, new ExternalUrl, 'unique:App\Models\RedirectModel,url_to'],
            'active' => ['boolean']
        ];
    }

    public function messages()
    {
        return [
            'url_to.url' => 'Url inválida.',
            'url_to.starts_with' => 'A url deve utilizar o protocolo https.',
            'url_to.unique' => 'Esta url já está em uso.',
            'active.boolean' => 'O campo active deve ser true ou false.'
        ];
    }
}
