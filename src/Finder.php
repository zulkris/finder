<?php

declare(strict_types=1);

namespace ZulKris\Finder;

use ZulKris\Finder\Exceptions\AccessDeniedException;
use ZulKris\Finder\Exceptions\FileNotFoundException;
use ZulKris\Finder\MimeTypeGuesser\MimeTypeGuesser;
use ZulKris\Finder\MimeTypeGuesser\MimeTypeGuesserInterface;
use ZulKris\Finder\Modifiers\EmptyModificator;
use ZulKris\Finder\Modifiers\ItemModificatorInterface;
use ZulKris\Finder\Settings\FinderSettings;
use ZulKris\Finder\Settings\YamlSettingsReader;

final class Finder
{
    private ItemModificatorInterface $itemModificator;
    private FinderSettings $finderSettings;
    private MimeTypeGuesserInterface $mimeTypeGuesser;

    public function __construct(
        FinderSettings $settingsReader = null,
        ItemModificatorInterface $itemModificator = null,
        MimeTypeGuesserInterface $mimeTypeGuesser = null
    ) {
        $this->itemModificator = $itemModificator ?? new EmptyModificator();
        $this->finderSettings = $settingsReader ??
            YamlSettingsReader::fromFile(__DIR__ . '/settings.yml')->getSettings();
        $this->mimeTypeGuesser = $mimeTypeGuesser ?? MimeTypeGuesser::getInstance();
    }

    public function find($needle, $filePath)
    {
        $fileInfo = new \SplFileInfo($filePath);

        if (false === $fileInfo->isFile()) {
            throw new FileNotFoundException('file is not found');
        }

        if (false === $fileInfo->isReadable()) {
            throw new AccessDeniedException('file is not readable');
        }

        if ($this->finderSettings->getMaxSizeMb() < $fileInfo->getSize() / 1024 / 1024) {
            throw new \RuntimeException('too big file');
        }

        if (
            !in_array(
                $mimeType = $this->mimeTypeGuesser->guess($filePath),
                $this->finderSettings->getAllowedMimeTypes(),
                true
            )
        ) {
            throw new \RuntimeException($mimeType . ' is not allowed Mime-Type, check settings');
        }

        $streamContext = $this->finderSettings->getStreamContext() ?
            stream_context_create($this->finderSettings->getStreamContext()) :
            null;
        $file = $fileInfo->openFile(
            'r',
            null,
            $streamContext
        );
        $file->rewind();

        $results = [];
        while (!$file->eof()) {
            $line = $file->fgets();

            if (false !== $pos = strpos($line, $this->itemModificator->modify($needle))) {
                $results[] = [
                    'line' => $file->key(),
                    'position' => $pos,
                ];

                if (true === $this->finderSettings->isStopAfterFirst()) {
                    break;
                }
            }
        }

        return $results;
    }
}
