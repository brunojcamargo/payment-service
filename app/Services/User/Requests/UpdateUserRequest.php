<?php

namespace App\Services\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Str;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            "fullName" => ['nullable', 'string'],
            "document" => ['nullable', 'min:11', 'max:14', 'unique:users,document,' . $userId],
            "email"  => ['nullable', 'email', 'unique:users,email,' . $userId],
            'password' => ['nullable', 'string', 'min:6', 'regex:/^(?=.*[A-Z])/'],
            "type"  => ['nullable', 'in:shopkeeper,common'],
        ];
    }

    protected function prepareForValidation()
    {
        if($this->input('document')){
            $this->merge([
                'document' => $this->transformDocument($this->input('document')),
            ]);
        }
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
