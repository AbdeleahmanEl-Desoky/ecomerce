<?php

declare(strict_types=1);

namespace Modules\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Ramsey\Uuid\Uuid;
use Modules\Product\DTO\CreateProductDTO;

class CreateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
        ];
    }

    public function createCreateProductDTO(): CreateProductDTO
    {
        return new CreateProductDTO(
            name: $this->get('name'),
        );
    }
}
