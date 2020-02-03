<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\SculpinUpdateBundle\Tests\Propagator;

use dflydev\util\antPathMatcher\AntPathMatcher;
use Kmchan\Sculpin\UpdateBundle\Propagator\ConfigFilesystemDataSourcePropagator;
use Kmchan\Sculpin\UpdateBundle\Tests\Marker\MockMarker;
use PHPUnit\Framework\TestCase;
use Sculpin\Core\SiteConfiguration\SiteConfigurationFactory;
use Sculpin\Core\Source\ConfigFilesystemDataSource;

/**
 * This test case validates the config filesystem data source propagator
 * class.
 */
class ConfigFilesystemDataSourcePropagatorTest extends TestCase
{
    /**
     * Test if the propagate method of the config filesystem data source
     * propagator works properly. The method should read the last build
     * time from a marker and assign it to the sinceTime property of a
     * config filesystem data source instance.
     */
    public function testPropagate(): void
    {
        $rootdir = getcwd();
        $factory = new SiteConfigurationFactory("$rootdir/config", 'dev');
        $matcher = new AntPathMatcher();
        $config = $factory->create();
        $source = new ConfigFilesystemDataSource($rootdir, $config, $factory, $matcher);

        $property = new \ReflectionProperty(ConfigFilesystemDataSource::class, 'sinceTime');
        $property->setAccessible(true);

        $now = date('c');
        $marker = new MockMarker($now);
        $propagator = new ConfigFilesystemDataSourcePropagator($marker, $source);

        $this->assertEquals('1970-01-01T00:00:00Z', $property->getValue($source));
        $propagator->propagate();
        $this->assertEquals($now, $property->getValue($source));
    }
}
