<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Tests\DependencyInjection;

use Kmchan\Sculpin\UpdateBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

/**
 * This test case validates the bundle configuration class.
 */
class ConfigurationTest extends TestCase
{
    /**
     * Test if the configuration can validate a single configuration tree
     * correctly.
     */
    public function testBasicConfiguration(): void
    {
        $configs = [
            [ 'marker' => [ 'file' => [ 'path' => './abc' ] ] ]
        ];

        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);

        $this->assertIsArray($config);
        $this->assertArrayHasKey('marker', $config);
        $this->assertIsArray($config['marker']);
        $this->assertArrayHasKey('file', $config['marker']);
        $this->assertIsArray($config['marker']['file']);
        $this->assertArrayHasKey('path', $config['marker']['file']);
        $this->assertEquals('./abc', $config['marker']['file']['path']);
    }

    /**
     * Test if the configuration can validate and merge multiple configuration
     * trees correctly.
     */
    public function testMergeConfiguration(): void
    {
        $configs = [
            [ 'marker' => [ 'file' => [ 'path' => './abc' ] ] ],
            [ 'marker' => [ 'file' => [ 'path' => './def' ] ] ]
        ];

        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);

        $this->assertIsArray($config);
        $this->assertArrayHasKey('marker', $config);
        $this->assertIsArray($config['marker']);
        $this->assertArrayHasKey('file', $config['marker']);
        $this->assertIsArray($config['marker']['file']);
        $this->assertArrayHasKey('path', $config['marker']['file']);
        $this->assertEquals('./def', $config['marker']['file']['path']);
    }
}
