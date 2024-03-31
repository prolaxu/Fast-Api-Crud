<?php

namespace Anil\FastApiCrud\Tests\TestClasses\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
