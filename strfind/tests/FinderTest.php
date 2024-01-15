<?php

use Mike4ip\Strfind\Finder;
use PHPUnit\Framework\TestCase;

class FinderTest extends TestCase
{
    public function testCanUseOnlyResource()
    {
        $finder = new Finder();

        $this->assertFalse(
            $finder->setResource(null)
        );

        $this->assertTrue(
            $finder->setResource(
                fopen(__DIR__ . '/../samples/sample-files/example-file.txt', 'r')
            )
        );
    }

    public function testLocalFilesOpenedCorrectly()
    {
        $finder = new Finder();
        $this->assertTrue(
            $finder->setLocalFile(__DIR__ . '/../samples/sample-files/example-file.txt')
        );
    }

    public function testConfigLoadedCorrectly()
    {
        $finder = new Finder();
        $this->assertTrue(
            $finder->setConfig(__DIR__ . '/../samples/sample-files/example-config.yml')
        );
    }
}
