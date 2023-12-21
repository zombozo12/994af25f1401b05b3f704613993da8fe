<?php

namespace App\Http\Requests;

use App\Http\Response\Response;
use Elegant\Sanitizer\Laravel\SanitizesInput;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class LoginRequest extends FormRequest
{
    use SanitizesInput;

    public function filters(): array
    {
        return [
            'email' => 'trim|lowercase',
        ];
    }

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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:filter', 'max:255', 'exists:users,email'],
            'password' => ['required', 'string']
        ];
    }

    /**
     * @throws HttpResponseException
     * @throws Exception
     */
    protected function failedValidation(Validator $validator)
    {
        $response = new Response(now(), $this->fingerprint());

        throw new HttpResponseException(
            $response->setValidateError($validator->errors()->getMessages(),
                HttpResponse::HTTP_BAD_REQUEST,
                'Failed validate login request.')
        );
    }
}
