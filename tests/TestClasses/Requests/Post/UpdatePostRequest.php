<?php

namespace Anil\FastApiCrud\Tests\TestClasses\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
