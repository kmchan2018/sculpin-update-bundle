<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Marker;

use InvalidArgumentException;
use Throwable;

/**
 * Update time marker represents some storage where the last build time of
 * a project is determined.
 */
interface MarkerInterface
{
    /**
     * Return the last build time of the current project in the
     * 'YYYY-MM-DDTHH:mm:ssZ' format. If the last build is not known,
     * UNIX epoch, aka '1970-01-01T00:00:00Z' should be returned.
     * @return string
     */
    public function read(): string;

    /**
     * Update the last build time of the current project to the current time.
     * @param string $timestamp Build time to be recorded. Optional.
     * @throws InvalidArgumentException if the given timestamp cannot be parsed.
     * @throws Throwable if timestamp cannot be written due to other reasons.
     */
    public function write(string $timestamp = null): void;
}
