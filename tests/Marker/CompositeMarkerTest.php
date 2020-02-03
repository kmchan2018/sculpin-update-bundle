<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Tests\Marker;

use Kmchan\Sculpin\UpdateBundle\Marker\CompositeMarker;
use PHPUnit\Framework\TestCase;

/**
 * This test case validates the composite marker class.
 */
class CompositeMarkerTest extends TestCase
{
    /**
     * Test if the read method of the composite marker works properly. The
     * method should invoke the read method of its children, and return the
     * ealiest result.
     */
    public function testRead(): void
    {
        $child1 = new MockMarker('1970-01-01T00:00:00Z');
        $child2 = new MockMarker('1971-01-01T00:00:00Z');
        $marker = new CompositeMarker();

        $marker->add($child1);
        $marker->add($child2);

        $this->assertEquals('1970-01-01T00:00:00Z', $marker->read());
    }

    /**
     * Test if the write method of the composite marker works properly. The
     * method should invoke the write method of its children.
     */
    public function testWrite(): void
    {
        $now = date('c');
        $child1 = new MockMarker('1970-01-01T00:00:00Z');
        $child2 = new MockMarker('1971-01-01T00:00:00Z');
        $marker = new CompositeMarker();

        $marker->add($child1);
        $marker->add($child2);
        $marker->write($now);

        $this->assertEquals($now, $child1->read());
        $this->assertEquals($now, $child2->read());
        $this->assertEquals($now, $marker->read());
    }
}
