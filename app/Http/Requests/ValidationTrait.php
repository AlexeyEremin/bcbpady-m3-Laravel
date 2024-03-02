<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

trait ValidationTrait
{
    /**
     * Переписанный обработчик валидации
     * @param  Validator  $validator
     * @return mixed
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();
        $response = new JsonResponse([
            'success' => false,
            'message' => $errors,
        ], 422);
        throw new HttpResponseException($response);
    }
}