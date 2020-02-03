<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle;

use Kmchan\Sculpin\UpdateBundle\DependencyInjection\Compiler\MarkerPass;
use Kmchan\Sculpin\UpdateBundle\DependencyInjection\Compiler\PropagatorPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Sculpin Bundle to implement an `update` command that rebuilds any sources
 * that have changed after last generate/update.
 */
class SculpinUpdateBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MarkerPass());
        $container->addCompilerPass(new PropagatorPass());
    }
}
