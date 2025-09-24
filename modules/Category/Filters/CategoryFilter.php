<?php

declare(strict_types=1);

namespace Modules\Category\Filters;

use BasePackage\Shared\Filters\SearchModelFilter;

class CategoryFilter extends SearchModelFilter
{
    public $relations = [];

    public function name($name)
    {
        return $this->where('name', 'like','%'.$name.'%');
    }
    public function slug($slug)
    {
        return $this->where('slug', 'like','%'.$slug.'%');
    }
    public function parent($parentId)
    {
        return $this->where('parent_id',$parentId);
    }
}
