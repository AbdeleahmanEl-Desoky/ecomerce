<?php

declare(strict_types=1);

namespace Modules\Category\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Ramsey\Uuid\Uuid;
use Modules\Category\DTO\CreateCategoryDTO;

class CreateCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|uuid|exists:categories,id',
        ];
    }

    public function createCreateCategoryDTO(): CreateCategoryDTO
    {
        return new CreateCategoryDTO(
            name: $this->get('name'),
            slug: $this->get('slug'),
            parent_id: $this->get('parent_id'),
        );
    }
}
