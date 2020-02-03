<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Marker;

/**
 * FileMarker implements a marker that tracks project build time with the
 * modification time of a special file.
 */
class FileMarker implements MarkerInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * Construct a new file marker using the given file under the given
     * output directory.
     * @param string $output Output directory
     * @param string $path Path of the special file relative to the output directory.
     */
    public function __construct(string $output, string $path)
    {
        $this->path = $output . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * {@inheritdoc}
     */
    public function read(): string
    {
        if (file_exists($this->path)) {
            if (($timestamp = filemtime($this->path)) !== false) {
                return date('c', $timestamp);
            }
        }

        return '1970-01-01T00:00:00Z';
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $timestamp = null): void
    {
        $time = time();

        if ($timestamp !== null) {
            if (($temp = strtotime($timestamp)) !== false) {
                $time = $temp;
            } else {
                throw new \InvalidArgumentException('invalid timestamp');
            }
        }

        @touch($this->path, $time);
    }
}
