<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Configuration define the config structure accepted by the bundle.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder();

        $builder
            ->root('sculpin_update')
                ->children()
                    ->arrayNode('marker')
                        ->children()
                            ->arrayNode('file')
                                ->children()
                                    ->scalarNode('path')
                                        ->defaultValue('.build_timestamp')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end();

        return $builder;
    }
}
