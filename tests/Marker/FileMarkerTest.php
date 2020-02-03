<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Tests\Marker;

use Kmchan\Sculpin\UpdateBundle\Marker\FileMarker;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

/**
 * This test case validates the file marker class.
 */
class FileMarkerTest extends TestCase
{
    /**
     * Test if the read method of the composite marker works properly. The
     * method should read the modification time of a marker file and return
     * it; when the marker file is missing, returns UNIX epoch instead.
     */
    public function testRead(): void
    {
        $root = vfsStream::setup('output');
        $output = vfsStream::url('output');
        $path = 'xxx';
        $marker = new FileMarker($output, $path);

        $this->assertEquals(0, strtotime($marker->read()));

        clearstatcache();
        touch($output . DIRECTORY_SEPARATOR . $path, 1000);
        $this->assertEquals(1000, strtotime($marker->read()));
    }

    /**
     * Test if the write method of the composite marker works properly. The
     * method should update the modification time of a marker file, creating
     * one if necessary.
     */
    public function testWrite(): void
    {
        $root = vfsStream::setup('output');
        $output = vfsStream::url('output');
        $path = 'xxx';
        $marker = new FileMarker($output, $path);

        clearstatcache();
        $marker->write(date('c', 1000));
        $this->assertEquals(1000, strtotime($marker->read()));

        clearstatcache();
        $marker->write(date('c', 2000));
        $this->assertEquals(2000, strtotime($marker->read()));
    }
}
