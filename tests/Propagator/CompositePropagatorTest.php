<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Tests\Propagator;

use Kmchan\Sculpin\UpdateBundle\Propagator\CompositePropagator;
use PHPUnit\Framework\TestCase;

/**
 * This test case validates the composite propagator class.
 */
class CompositePropagatorTest extends TestCase
{
    /**
     * Test if the propagate method of the composite propagator works properly.
     * The method should invoke the propagate method of its children.
     */
    public function testPropagate(): void
    {
        $child1 = new MockPropagator();
        $child2 = new MockPropagator();
        $propagator = new CompositePropagator();

        $propagator->add($child1);
        $propagator->add($child2);
        $this->assertFalse($child1->check());
        $this->assertFalse($child2->check());

        $propagator->propagate();
        $this->assertTrue($child1->check());
        $this->assertTrue($child2->check());
    }
}
