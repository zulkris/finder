<?php
declare(strict_types=1);

namespace ZulKris\Finder\Modifiers;

interface ItemModificationInterface
{
    public function modify($item): string;
}