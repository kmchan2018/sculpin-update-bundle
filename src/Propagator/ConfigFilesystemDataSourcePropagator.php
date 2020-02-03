<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Propagator;

use Kmchan\Sculpin\UpdateBundle\Marker\MarkerInterface;
use Sculpin\Core\Source\ConfigFilesystemDataSource;

/**
 * This propagator is responsible to propagate last build time from the given
 * marker to an instance of Sculpin ConfigFilesystemDataSource class.
 *
 * The current implementation of this class is quite hacky. The original
 * Sculpin class is declared final and the since time property is declared
 * private. It makes a normal fix impossible. Because of that, we use reflection
 * to crack open the protection and write to that property.
 *
 * Substantial changes to the implementation will break this class.
 */
class ConfigFilesystemDataSourcePropagator implements PropagatorInterface
{
    /**
     * @var MarkerInterface
     */
    private $marker;

    /**
     * @var ConfigFilesystemDataSource
     */
    private $destination;

    /**
     * Construct a new propagator from the given builder to the given config
     * filesystem data source instance.
     * @param MarkerInterface $marker Source of last build time.
     * @param ConfigFilesystemDataSource $destination Destination of the last build time.
     */
    public function __construct(MarkerInterface $marker, ConfigFilesystemDataSource $destination)
    {
        $this->marker = $marker;
        $this->destination = $destination;
    }

    /**
     * {@inheritdoc}
     */
    public function propagate(): void
    {
        $field = new \ReflectionProperty('Sculpin\Core\Source\ConfigFilesystemDataSource', 'sinceTime');
        $field->setAccessible(true);
        $field->setValue($this->destination, $this->marker->read());
        $field->setAccessible(false);
    }
}
