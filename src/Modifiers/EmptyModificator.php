<?php
declare(strict_types=1);

namespace ZulKris\Finder\Modifiers;

class EmptyModification implements ItemModificationInterface
{
    public function modify($item): string
    {
        return $item;
    }
}