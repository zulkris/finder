<?php

namespace ZulKris\Finder\Settings;

class FinderSettings
{
    private array $allowedMimeTypes;
    private int $maxSizeMb;
    private bool $stopAfterFirst;

    public static function createFromArray(array $settingsArray): self
    {
        $settings = new self();
        $settings->maxSizeMb = $settingsArray['finder']['max_size_mb'] ?? 0;
        $settings->allowedMimeTypes = $settingsArray['finder']['mime_types'] ?? ['txt'];
        $settings->stopAfterFirst = $settingsArray['finder']['stop_after_first'] ?? true;

        return $settings;
    }

    /**
     * @return int
     */
    public function getMaxSizeMb(): int
    {
        return $this->maxSizeMb;
    }

    /**
     * @return bool
     */
    public function isStopAfterFirst(): bool
    {
        return $this->stopAfterFirst;
    }

    /**
     * @return array
     */
    public function getAllowedMimeTypes(): array
    {
        return $this->allowedMimeTypes;
    }
}
