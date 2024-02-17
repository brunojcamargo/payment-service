<?php

namespace App\Services\Transaction\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
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
            "from" => ['required', 'string', Rule::exists('users', 'id')],
            "to" => ['required', 'string', Rule::exists('users', 'id')],
            "value" => ['required', 'numeric', 'min:0.01']
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'value' => $this->transformValue($this->input('value')),
        ]);
    }

    private function transformValue($value)
    {
        $cleanValue = preg_replace('/[^\d,.]/', '', $value);
        $cleanValue = str_replace(',', '.', $cleanValue);
        return (float) $cleanValue;
    }

    public function messages()
    {
        return [
            'from.required' => 'O campo from é obrigatório.',
            'from.string' => 'O campo from deve ser uma string.',
            'from.exists' => 'O usuário de origem informado não existe.',
            'to.required' => 'O campo to é obrigatório.',
            'to.string' => 'O campo to deve ser uma string.',
            'to.exists' => 'O usuário de destino informado não existe.',
            'value.required' => 'O campo value é obrigatório.',
            'value.numeric' => 'O campo value deve ser um número.',
            'value.min' => 'O valor mínimo para o campo value é :min.',
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
