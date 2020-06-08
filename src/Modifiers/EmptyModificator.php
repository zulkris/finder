<?php

declare(strict_types=1);

namespace ZulKris\Finder\Modifiers;

class EmptyModificator implements ItemModificatorInterface
{
    public function modify($item): string
    {
        return $item;
    }
}
