<?php

declare(strict_types=1);

namespace Modules\Product\Filters;

use BasePackage\Shared\Filters\SearchModelFilter;

class ProductFilter extends SearchModelFilter
{
       public $relations = [];

        public function name($name)
        {
            return $this->where('name', $name);
        }
}
