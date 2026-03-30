<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Visitor;

use FunTask\Domain\Category\Category;

interface CategoryVisitor
{
    public function enter(Category $category): void;
    public function leave(Category $category): void;
}
