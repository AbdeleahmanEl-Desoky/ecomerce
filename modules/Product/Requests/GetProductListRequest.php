<?php

declare(strict_types=1);

namespace Modules\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Ramsey\Uuid\Uuid;

class GetProductListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'per_page' => 'integer',
            'page' => 'integer',
        ];
    }
}
