<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * This compiler pass adds all services tagged `update.marker` to the
 * root composite marker.
 */
class MarkerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('sculpin_update.marker')) {
            $definition = $container->getDefinition('sculpin_update.marker');
            $children = $container->findTaggedServiceIds('sculpin_update.marker');

            foreach ($children as $id => $attributes) {
                $definition->addMethodCall('add', [ new Reference($id) ]);
            }
        }
    }
}
