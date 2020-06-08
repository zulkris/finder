<?php

declare(strict_types=1);

namespace ZulKris\Tests;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use ZulKris\Finder\Exceptions\AccessDeniedException;
use ZulKris\Finder\Exceptions\FileNotFoundException;
use ZulKris\Finder\Finder;
use ZulKris\Finder\Settings\YamlSettingsReader;

class FinderTest extends TestCase
{
    private vfsStreamDirectory $fileSystem;
    private \ZulKris\Finder\Finder $finder;
    private array $directoryStructure;

    public function setUp(): void
    {
        $settingsYml = <<<FILE
finder:
  max_size_mb: 50
  mime_types:
    - 'text/plain'
  stop_after_first: true
FILE;

        $this->directoryStructure = [
            'files' => [
                'jsonfile' => '{"VALID_KEY":123}',
                'textfile.txt' => 'aaa bbb
                aaa ccc ddd
                eee aaa',
                'settings.yml' => $settingsYml
            ]
        ];

        $this->fileSystem = vfsStream::setup('root', 444, $this->directoryStructure);
        $yamlSettingsReader = YamlSettingsReader::fromFile($this->fileSystem->url() . '/files/settings.yml');
        $this->finder = new Finder($yamlSettingsReader->getSettings());
    }

    public function testFileNotFound()
    {
        $this->expectException(FileNotFoundException::class);
        $this->finder->find('aaa', $this->fileSystem->url() . '/files/abc.txt');
    }

    public function testFileMimeType()
    {
        $this->expectException(\RuntimeException::class);
        $this->finder->find('aaa', $this->fileSystem->url() . '/files/jsonfile');
    }

    public function testStopAfterFirst()
    {
        $settingsYml = <<<FILE
finder:
  max_size_mb: 50
  mime_types:
    - 'text/plain'
  stop_after_first: false
FILE;

        //$this->fileSystem = vfsStream::setup('root', 444, $this->directoryStructure);
        $yamlSettingsReader = YamlSettingsReader::fromString($settingsYml);
        $finder = new Finder($yamlSettingsReader->getSettings());

        $results = $finder->find('aaa', $this->fileSystem->url() . '/files/textfile.txt');

        $this->assertCount(3, $results);
    }
}
