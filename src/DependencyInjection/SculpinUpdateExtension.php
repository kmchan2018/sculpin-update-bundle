<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Symfony dependency injection extension to hook the bundle up to
 * sculpin.
 */
class SculpinUpdateExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('sculpin_update.marker.file.path', '.build_timestamp');

        if (array_key_exists('marker', $config)) {
            if (array_key_exists('file', $config['marker'])) {
                if (array_key_exists('path', $config['marker']['file'])) {
                    $container->setParameter('sculpin_update.marker.file.path', $config['marker']['file']['path']);
                }
            }
        }
    }
}
