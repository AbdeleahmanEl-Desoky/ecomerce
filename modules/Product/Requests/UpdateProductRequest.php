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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'required|string|max:255|unique:products,sku,' . $this->route('id'),
            'status' => 'required|in:active,inactive',
            'category_id' => 'required|uuid|exists:categories,id',
        ];
    }

    public function createUpdateProductCommand(): UpdateProductCommand
    {
        return new UpdateProductCommand(
            id: Uuid::fromString($this->route('id')),
            name: $this->get('name'),
            description: $this->get('description'),
            price: (float) $this->get('price'),
            stock_quantity: (int) $this->get('stock_quantity'),
            sku: $this->get('sku'),
            status: $this->get('status'),
            category_id: Uuid::fromString($this->get('category_id')),
        );
    }
}
