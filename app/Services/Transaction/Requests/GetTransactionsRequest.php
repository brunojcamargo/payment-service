<?php

namespace App\Services\Transaction\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Validation\Rule;

class GetTransactionsRequest extends FormRequest
{
    protected $rule;

    public function __construct(Rule $rule)
    {
        $this->rule = $rule;
    }

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
            "from" => ['required', 'string', $this->rule->exists('users', 'id')]
        ];
    }

    public function messages()
    {
        return [
            'from.required' => 'O campo from é obrigatório.',
            'from.string' => 'O campo from deve ser uma string.',
            'from.exists' => 'O usuário de origem informado não existe.'
        ];
    }

    public function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(response()->json([
            'error'   => true,
            'code'   => HttpResponse::HTTP_UNPROCESSABLE_ENTITY,
            'data'      => $validator->errors()
        ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
