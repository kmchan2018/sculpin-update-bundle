<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Marker;

/**
 * Composite marker combines multiple child marker into a single marker.
 * Reads will return the earliest result, and writes will be forwarded to
 * all children.
 */
class CompositeMarker implements MarkerInterface
{
    /**
     * @var MarkerInterface[]
     */
    private $children;

    /**
     * Construct an empty composite marker. Child markers can be added later
     * by the `add` method.
     */
    public function __construct()
    {
        $this->children = [];
    }

    /**
     * Add a child marker to the composite.
     * @param MarkerInterface $marker Marker to be added.
     */
    public function add(MarkerInterface $marker): void
    {
        $this->children[] = $marker;
    }

    /**
     * {@inheritdoc}
     */
    public function read(): string
    {
        $selectedTimestamp = time();
        $selectedOutput = date('c', $selectedTimestamp);

        foreach ($this->children as $child) {
            $candidateOutput = $child->read();
            $candidateTimestamp = strtotime($candidateOutput);

            if ($candidateTimestamp < $selectedTimestamp) {
                $selectedTimestamp = $candidateTimestamp;
                $selectedOutput = $candidateOutput;
            }
        }

        return $selectedOutput;
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $timestamp = null): void
    {
        if ($timestamp === null) {
            $timestamp = date('c');
        }

        foreach ($this->children as $child) {
            $child->write($timestamp);
        }
    }
}
