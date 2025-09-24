<?php

declare(strict_types=1);

namespace Modules\Order\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Ramsey\Uuid\Uuid;
use Modules\Order\DTO\CreateOrderDTO;

class CreateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
        ];
    }

    public function createCreateOrderDTO(): CreateOrderDTO
    {
        return new CreateOrderDTO(
            name: $this->get('name'),
        );
    }
}
