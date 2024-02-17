<?php

namespace App\Services\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response as HttpResponse;

use Illuminate\Support\Str;

class CreateUserRequest extends FormRequest
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
            "fullName" => ['required', 'string'],
            "document" => ['required', 'min:11', 'max:14', 'unique:App\Models\User,document'],
            "email"  => ['required', 'email', 'unique:App\Models\User,email'],
            'password' => ['required', 'string', 'min:6', 'regex:/^(?=.*[A-Z])/'],
            "type"  => ['required', 'in:shopkeeper,common'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'document' => $this->transformDocument($this->input('document')),
        ]);
    }

    private function transformDocument($document)
    {
        return Str::of($document)->replace(['.', '-', '/'], '')->__toString();
    }

    public function messages()
    {
        return [
            'fullName.required' => 'O campo nome completo é obrigatório.',
            'document.required' => 'O campo documento é obrigatório.',
            'document.unique' => 'Este documento já está em uso.',
            'document.min' => 'O documento deve ter no mínimo :min caracteres.',
            'document.max' => 'O documento deve ter no máximo :max caracteres.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O email deve ter um formato válido.',
            'email.unique' => 'Este email já está em uso.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter no mínimo :min caracteres.',
            'password.regex' => 'A senha deve conter pelo menos uma letra maiúscula.',
            'type.required' => 'O campo tipo é obrigatório.',
            'type.in' => 'O tipo deve ser shopkeeper ou common.',
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
