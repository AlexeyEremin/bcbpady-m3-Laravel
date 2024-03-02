<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class AccessRequest extends FormRequest
{
    use ValidationTrait;

    public function authorize()
    {
        $user = auth()->user();
        return $this->file->access()->where([
            'author' => 1,
            'user_id' => $user->id
        ])->exists() && $user->email != $this->request->get('email', null);
    }

    protected function failedAuthorization()
    {
        $response = new JsonResponse([
            "message" => "Forbidden for you",
        ], 403);
        throw new HttpResponseException($response);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "email" => "required|email|exists:users",
        ];
    }
}
