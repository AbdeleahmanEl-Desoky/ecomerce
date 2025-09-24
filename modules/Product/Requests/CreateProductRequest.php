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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'required|string|max:255|unique:products,sku',
            'status' => 'required|in:active,inactive',
            'category_id' => 'required|uuid|exists:categories,id',
        ];
    }

    public function createCreateProductDTO(): CreateProductDTO
    {
        return new CreateProductDTO(
            name: $this->get('name'),
            description: $this->get('description'),
            price: (float) $this->get('price'),
            stock_quantity: (int) $this->get('stock_quantity'),
            sku: $this->get('sku'),
            status: $this->get('status', 'active'),
            category_id: Uuid::fromString($this->get('category_id')),
        );
    }
}
