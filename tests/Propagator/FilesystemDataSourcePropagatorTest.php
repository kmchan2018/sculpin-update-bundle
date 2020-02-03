<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Tests\Source;

use Dflydev\Canal\Analyzer\Analyzer;
use Dflydev\Canal\Detector\ApacheMimeTypesExtensionDetector;
use dflydev\util\antPathMatcher\AntPathMatcher;
use Kmchan\Sculpin\UpdateBundle\Propagator\FilesystemDataSourcePropagator;
use Kmchan\Sculpin\UpdateBundle\Source\ReplacementFilesystemDataSource;
use Kmchan\Sculpin\UpdateBundle\Tests\Marker\MockMarker;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Sculpin\Core\Source\SourceSet;

/**
 * This test case validates the filesystem data source propagator.
 */
class FilesystemDataSourcePropagatorTest extends TestCase
{
    /**
     * Test if the propagate function works properly.
     */
    public function testPropagate()
    {
        vfsStream::setup("sources");

        $path = vfsStream::url("sources");

        $matcher = new AntPathMatcher();
        $detector = new ApacheMimeTypesExtensionDetector();
        $analyzer = new Analyzer($detector);
        $source = new ReplacementFilesystemDataSource($path, [], [], [], $matcher, $analyzer);
        $set = new SourceSet();

        $marker = new MockMarker(date('c', 1500));
        $propagator = new FilesystemDataSourcePropagator($marker, $source);

        touch($path . DIRECTORY_SEPARATOR . 'source1', 1000);
        touch($path . DIRECTORY_SEPARATOR . 'source2', 2000);
        sleep(1);
        clearstatcache();

        $set->reset();
        $propagator->propagate();
        $source->refresh($set);
        $this->assertEquals(2, count($set->allSources()));
        $this->assertEquals(2, count($set->newSources()));
        $this->assertEquals(1, count($set->updatedSources()));

        touch($path . DIRECTORY_SEPARATOR . 'source2');
        touch($path . DIRECTORY_SEPARATOR . 'source3');
        sleep(1);
        clearstatcache();

        $set->reset();
        $source->refresh($set);
        $this->assertEquals(3, count($set->allSources()));
        $this->assertEquals(1, count($set->newSources()));
        $this->assertEquals(2, count($set->updatedSources()));

        sleep(1);
        clearstatcache();

        $set->reset();
        $source->refresh($set);
        $this->assertEquals(3, count($set->allSources()));
        $this->assertEquals(0, count($set->newSources()));
        $this->assertEquals(0, count($set->updatedSources()));
    }
}
