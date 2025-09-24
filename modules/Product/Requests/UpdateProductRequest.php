<?php

declare(strict_types=1);

namespace Modules\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Ramsey\Uuid\Uuid;
use Modules\Product\Commands\UpdateProductCommand;
use Modules\Product\Handlers\UpdateProductHandler;

class UpdateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
        ];
    }

    public function createUpdateProductCommand(): UpdateProductCommand
    {
        return new UpdateProductCommand(
            id: Uuid::fromString($this->route('id')),
            name: $this->get('name'),
        );
    }
}
