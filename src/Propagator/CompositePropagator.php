<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Propagator;

/**
 * Composite propagator combines multiple child propagators into a single
 * propagator. Any propagation request will be forwarded to all children.
 */
class CompositePropagator implements PropagatorInterface
{
    /**
     * @var PropagatorInterface[]
     */
    private $children;

    /**
     * Construct an empty composite propagator. Child propagators can be added
     * later by the `add` method.
     */
    public function __construct()
    {
        $this->children = [];
    }

    /**
     * Add a child propagator to the composite.
     * @param PropagatorInterface $propagator Propagator to be added.
     */
    public function add(PropagatorInterface $propagator): void
    {
        $this->children[] = $propagator;
    }

    /**
     * Propagate last build time.
     */
    public function propagate(): void
    {
        foreach ($this->children as $child) {
            $child->propagate();
        }
    }
}
