<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddFileRequest extends FormRequest
{
    use ValidationTrait;
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
        # mimes https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
        # attributes validation max in size kilobytes
        return [
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:docx,pdf,doc,zip,jpeg,jpg,png|max:2048'
        ];
    }
}
