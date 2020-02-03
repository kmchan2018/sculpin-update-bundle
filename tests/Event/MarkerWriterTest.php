<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Tests\Event;

use Kmchan\Sculpin\UpdateBundle\Event\MarkerWriter;
use Kmchan\Sculpin\UpdateBundle\Tests\Marker\MockMarker;
use PHPUnit\Framework\TestCase;
use Sculpin\Core\Sculpin;
use Sculpin\Core\Event\SourceSetEvent;
use Sculpin\Core\Source\SourceSet;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * This test case validates the marker writer class.
 */
class MarkerWriterTest extends TestCase
{
    /**
     * Test if the marker writer can subscribe and handle the sculpin after
     * run event correctly. When the event is dispatched, the class should
     * write the current time to a marker.
     */
    public function testOnAfterRun(): void
    {
        $marker = new MockMarker('1970-01-01T00:00:00Z');
        $writer = new MarkerWriter($marker);

        $set = new SourceSet();
        $event = new SourceSetEvent($set);
        $dispatcher = new EventDispatcher();

        $this->assertEquals('1970-01-01T00:00:00Z', $marker->read());
        $dispatcher->addSubscriber($writer);
        $dispatcher->dispatch($event, Sculpin::EVENT_AFTER_RUN);
        $this->assertNotEquals('1970-01-01T00:00:00Z', $marker->read());
    }
}
