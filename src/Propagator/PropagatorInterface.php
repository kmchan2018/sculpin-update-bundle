<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Propagator;

/**
 * Update time propagator is responsible for propagating last build time from
 * update time marker to other sculpin services.
 */
interface PropagatorInterface
{
    /**
     * Propagate last build time.
     */
    public function propagate(): void;
}
