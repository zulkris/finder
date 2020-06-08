<?php

declare(strict_types=1);

namespace ZulKris\Finder\Modifiers;

interface ItemModificatorInterface
{
    public function modify($item): string;
}
