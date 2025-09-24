<?php

declare(strict_types=1);

namespace Modules\Category\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Ramsey\Uuid\Uuid;
use Modules\Category\Commands\UpdateCategoryCommand;
use Modules\Category\Handlers\UpdateCategoryHandler;

class UpdateCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        $categoryId = $this->route('id');
        
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $categoryId,
            'parent_id' => 'nullable|uuid|exists:categories,id|not_in:' . $categoryId,
        ];
    }

    public function createUpdateCategoryCommand(): UpdateCategoryCommand
    {
        return new UpdateCategoryCommand(
            id: Uuid::fromString($this->route('id')),
            name: $this->get('name'),
            slug: $this->get('slug'),
            parent_id: $this->get('parent_id'),
        );
    }
}
