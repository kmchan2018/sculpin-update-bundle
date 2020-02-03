<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\DependencyInjection\Compiler;

use Kmchan\Sculpin\UpdateBundle\Propagator\ConfigFilesystemDataSourcePropagator;
use Kmchan\Sculpin\UpdateBundle\Propagator\FilesystemDataSourcePropagator;
use Kmchan\Sculpin\UpdateBundle\Source\ReplacementFilesystemDataSource;
use Sculpin\Core\Source\ConfigFilesystemDataSource;
use Sculpin\Core\Source\FilesystemDataSource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * This compiler pass adds all services tagged `update.propagator` to the
 * root composite propagator. It also creates a propagator for each
 * FilesystemDataSource and ConfigFilesystemDataSource serivce in the
 * container.
 */
class PropagatorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('sculpin_update.propagator') === false) {
            return;
        }

        $propagatorDefinition = $container->getDefinition('sculpin_update.propagator');
        $markerReference = new Reference('sculpin_update.marker');

        foreach ($container->findTaggedServiceIds('sculpin_update.propagator') as $id => $attributes) {
            $propagatorDefinition->addMethodCall('add', [ new Reference($id) ]);
        }

        foreach ($container->getDefinitions() as $serviceId => $serviceDefinition) {
            $serviceClass = $serviceDefinition->getClass();

            if ($serviceClass === null) {
                continue;
            }

            if ($serviceDefinition->hasTag('sculpin.data_source') === false) {
                continue;
            }

            if (strcasecmp($serviceClass, FilesystemDataSource::class) === 0) {
                $serviceReference = new Reference($serviceId);
                $childId = 'sculpin_update.propagator.' . $serviceId;
                $childReference = new Reference($childId);
                $childDefinition = new Definition(FilesystemDataSourcePropagator::class, [ $markerReference, $serviceReference ]);

                $container->setDefinition($childId, $childDefinition);
                $serviceDefinition->setClass(ReplacementFilesystemDataSource::class);
                $propagatorDefinition->addMethodCall('add', [ $childReference ]);
            }

            if (strcasecmp($serviceClass, ConfigFilesystemDataSource::class) === 0) {
                $serviceReference = new Reference($serviceId);
                $childId = 'sculpin_update.propagator.' . $serviceId;
                $childReference = new Reference($childId);
                $childDefinition = new Definition(ConfigFilesystemDataSourcePropagator::class, [ $markerReference, $serviceReference ]);

                $container->setDefinition($childId, $childDefinition);
                $propagatorDefinition->addMethodCall('add', [ $childReference ]);
            }
        }
    }
}
