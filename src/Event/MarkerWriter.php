<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Event;

use Kmchan\Sculpin\UpdateBundle\Marker\MarkerInterface;
use Sculpin\Core\Sculpin;
use Sculpin\Core\Event\SourceSetEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MarkerWriter implements EventSubscriberInterface
{
    /**
     * @var MarkerInterface
     */
    private $marker;

    /**
     * Construct a new marker writer that writes the given marker at the
     * end of each run.
     * @param MarkerInterface $marker Marker to be written.
     */
    public function __construct(MarkerInterface $marker)
    {
        $this->marker = $marker;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Sculpin::EVENT_AFTER_RUN => 'onAfterRun',
        ];
    }

    /**
     * Handle the sculpin lifecycle event to update the marker file
     * after each run.
     */
    public function onAfterRun(SourceSetEvent $event): void
    {
        $this->marker->write(date('c'));
    }
}
