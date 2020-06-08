<?php

declare(strict_types=1);

namespace ZulKris\Finder\Settings;

interface SettingsReaderInterface
{
    public function getSettings(): FinderSettings;
}
