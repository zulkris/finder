<?php

declare(strict_types=1);

namespace ZulKris\Finder\Settings;

use Symfony\Component\Yaml\Yaml;

class YamlSettingsReader implements SettingsReaderInterface
{
    private $parsed;

    public static function fromFile(string $filePath)
    {
        $ymlSettingsReader = new self();
        $ymlSettingsReader->parsed = Yaml::parseFile($filePath);
        return $ymlSettingsReader;
    }

    public static function fromString(string $filePath)
    {
        $ymlSettingsReader = new self();
        $ymlSettingsReader->parsed = Yaml::parse($filePath);
        return $ymlSettingsReader;
    }

    public function getSettings(): FinderSettings
    {
        return FinderSettings::createFromArray($this->parsed);
    }
}
