<?php

namespace Tests\Differ;

use function \Differ\Differ\genDiff as genDiff;
use PHPUnit\Framework\TestCase;

class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $firstFile = 'file1.json';
        $secondFile = 'file2.json';
        $result = "- follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true";

        $diff = genDiff($firstFile, $secondFile, 'json');

        $this->assertEquals($diff, $result);
//        $this->assertEquals(collect($children), $user->getChildren());
    }
}