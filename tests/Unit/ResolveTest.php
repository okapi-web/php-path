<?php

namespace Okapi\Path\Tests\Unit;

use Okapi\Path\Path;
use PHPUnit\Framework\TestCase;

class ResolveTest extends TestCase
{
    public function testResolve(): void
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'test';
        $path2 = __DIR__ . "\\test";
        $path3 = __DIR__ . "/test";

        $this->assertEquals(
            $path,
            Path::resolve($path)
        );

        $this->assertEquals(
            $path,
            Path::resolve($path2)
        );

        $this->assertEquals(
            $path,
            Path::resolve($path3)
        );
    }

    public function testEmptyPath(): void
    {
        $this->assertEquals(
            '',
            Path::resolve('')
        );
    }

    public function testArray(): void
    {
        $paths = [
            __DIR__ . DIRECTORY_SEPARATOR . 'test',
            __DIR__ . DIRECTORY_SEPARATOR . 'test2',
        ];

        $result = Path::resolve($paths);

        foreach ($paths as $i => $path) {
            $this->assertEquals(
                $path,
                $result[$i]
            );
        }
    }

    public function testScheme(): void
    {
        $start = 'test://';
        $end = __DIR__ . DIRECTORY_SEPARATOR . 'test.php';
        $path = "$start$end";
        $result = Path::resolve($path);

        $this->assertStringStartsWith(
            $start,
            $result
        );

        $this->assertStringEndsWith(
            $end,
            $result
        );
    }

    public function testStreamResolveIncludePath(): void
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'tests/Stubs/test.php';
        $result = Path::resolve($path);

        $this->assertEquals(
            __DIR__ . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'Stubs' . DIRECTORY_SEPARATOR . 'test.php',
            $result
        );
    }

    public function testRelativePath(): void
    {
        $path = './test.php';
        $result = Path::resolve($path);

        $this->assertStringEndsWith(
            DIRECTORY_SEPARATOR . 'test.php',
            $result
        );

        $path = '../test.php';
        $result = Path::resolve($path);

        $this->assertStringEndsWith(
            DIRECTORY_SEPARATOR . 'test.php',
            $result
        );
    }

    public function testCheckExistence(): void
    {
        $path = 'tests/Stubs/test.php';
        $result = Path::resolve($path, true);

        $this->assertStringEndsWith(
            DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'Stubs' . DIRECTORY_SEPARATOR . 'test.php',
            $result
        );

        $nonExistingPath = 'tests/Stubs/test2.php';
        $result = Path::resolve($nonExistingPath, true);

        $this->assertFalse($result);
    }
}
