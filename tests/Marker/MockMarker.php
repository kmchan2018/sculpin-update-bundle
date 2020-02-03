<?php

namespace Kmchan\Sculpin\UpdateBundle\Tests\Marker;

use Kmchan\Sculpin\UpdateBundle\Marker\MarkerInterface;

/**
 * Mock marker for use in unit tests. The class will track the last build time
 * in a private property. The constructor will initialize the property; the
 * read method will return its value and the write method will update its
 * value.
 */
class MockMarker implements MarkerInterface
{
    /**
     * @var string
     */
    private $timestamp;

    /**
     * Construct a new mock marker instance.
     * @param string $date
     */
    public function __construct(string $date)
    {
        $this->timestamp = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function read(): string
    {
        return $this->timestamp;
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $timestamp = null): void
    {
        $this->timestamp = $timestamp ?? date('c');
    }
}
