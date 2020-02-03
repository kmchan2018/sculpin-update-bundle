<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Propagator;

use Kmchan\Sculpin\UpdateBundle\Marker\MarkerInterface;
use Kmchan\Sculpin\UpdateBundle\Source\ReplacementFilesystemDataSource;
use Sculpin\Core\Source\FilesystemDataSource;

/**
 * This propagator is responsible to propagate last build time from the given
 * marker to sculpin filesystem data source service.
 *
 * Note that since filesystem data source service is patched by the bundle to
 * use the replacement class in the bundle, this class operates on that class
 * instead of the vanilla filesystem data source class despite the name.
 */
class FilesystemDataSourcePropagator implements PropagatorInterface
{
    /**
     * @var MarkerInterface
     */
    private $marker;

    /**
     * @var ReplacementFilesystemDataSource
     */
    private $destination;

    /**
     * Construct a new propagator from the given builder to the given filesystem
     * data source instance.
     * @param MarkerInterface $marker Source of last build time.
     * @param ReplacementFilesystemDataSource $destination Destination of the last build time.
     */
    public function __construct(MarkerInterface $marker, ReplacementFilesystemDataSource $destination)
    {
        $this->marker = $marker;
        $this->destination = $destination;
    }

    /**
     * {@inheritdoc}
     */
    public function propagate(): void
    {
        $this->destination->setInitialSinceTime($this->marker->read());
    }
}
