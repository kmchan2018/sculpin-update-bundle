<?php

namespace Kmchan\Sculpin\UpdateBundle\Tests\Propagator;

use Kmchan\Sculpin\UpdateBundle\Propagator\PropagatorInterface;

/**
 * Mock propagator for use in unit tests. The class do not perform any
 * propagation. It only tracks whether the propagate method is called
 * since its construction or most recent reset.
 */
class MockPropagator implements PropagatorInterface
{
    /**
     * @var bool
     */
    private $propagated;

    /**
     * Construct a new mock propagator instance.
     */
    public function __construct()
    {
        $this->propagated = false;
    }

    /**
     * Check if the propagate method is called since construction or last
     * reset.
     * @return bool
     */
    public function check(): bool
    {
        return $this->propagated;
    }

    /**
     * Reset the mock propagator.
     * @return bool
     */
    public function reset(): void
    {
        $this->propagated = false;
    }

    /**
     * {@inheritdoc}
     */
    public function propagate(): void
    {
        $this->propagated = true;
    }
}
