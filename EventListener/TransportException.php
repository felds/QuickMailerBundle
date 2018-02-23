<?php
declare(strict_types=1);

namespace Felds\QuickMailerBundle\EventListener;

use Psr\Log\LoggerInterface;
use Swift_Events_TransportExceptionEvent;
use Swift_Events_TransportExceptionListener;

/**
 * Screams when the transport fails.
 */
class TransportException implements Swift_Events_TransportExceptionListener
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Swift_Events_TransportExceptionEvent $event
     */
    public function exceptionThrown(Swift_Events_TransportExceptionEvent $event)
    {
        $this->logger->critical("Transport error: {$event->getException()->getMessage()}");
    }
}
