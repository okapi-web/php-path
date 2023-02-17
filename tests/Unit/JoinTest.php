<?php

namespace Okapi\Path\Tests\Unit;

use Okapi\Path\Path;
use PHPUnit\Framework\TestCase;

class JoinTest extends TestCase
{
    public function testJoin(): void
    {
        $paths = [__DIR__, 'test', 'test2'];

        $this->assertEquals(
            implode(DIRECTORY_SEPARATOR, $paths),
            Path::join(...$paths)
        );
    }

    public function testNoPaths(): void
    {
        $this->assertEquals(
            '.',
            Path::join()
        );
    }

    public function testEmptyPath(): void
    {
        $this->assertEquals(
            '.',
            Path::join('')
        );
    }
}
