<?php

use Mike4ip\Strfind\Finder;
use Mike4ip\Strfind\Provider\Substr;
use PHPUnit\Framework\TestCase;

class SubstrTest extends TestCase
{
    public function testSearchWorking()
    {
        $finder = new Finder();
        $finder->setProvider(
            new Substr()
        );
        $finder->setLocalFile(__DIR__ . '/../samples/sample-files/example-file.txt');
        $result = $finder->find('aliquam');

        $this->assertTrue($result->found);
        $this->assertEquals(1, $result->line);
        $this->assertEquals(143, $result->row);
        $this->assertStringStartsWith('Lorem ipsum dolor sit amet', $result->string);
    }
}
