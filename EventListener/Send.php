<?php
declare(strict_types=1);

namespace Felds\QuickMailerBundle\EventListener;

use Psr\Log\LoggerInterface;
use Swift_Events_SendEvent;
use Swift_Events_SendListener;

class Send implements Swift_Events_SendListener
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SendListener constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Invoked immediately before the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        $this->log($evt);
    }

    /**
     * Invoked immediately after the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
        $this->log($evt);
    }

    private function log(Swift_Events_SendEvent $evt)
    {
        switch ($evt->getResult()) {
            case Swift_Events_SendEvent::RESULT_PENDING:
                // $this->logger->debug("Pending email.", ['id' => $evt->getMessage()->getId()]);
                break;
            case Swift_Events_SendEvent::RESULT_SPOOLED:
                $this->logger->debug("Email spooled.", ['id' => $evt->getMessage()->getId()]);
                break;
            case Swift_Events_SendEvent::RESULT_SUCCESS:
                $this->logger->debug("Email successfully sent!", ['id' => $evt->getMessage()->getId()]);
                break;
            case Swift_Events_SendEvent::RESULT_TENTATIVE:
                $this->logger->critical("Some emails failed.", ['id' => $evt->getMessage()->getId(), 'recipients' => $evt->getFailedRecipients()]);
                break;
            case Swift_Events_SendEvent::RESULT_FAILED:
                $this->logger->critical("Sending failed.", ['id' => $evt->getMessage()->getId(), 'recipients' => $evt->getFailedRecipients()]);
                break;
        }
    }
}
