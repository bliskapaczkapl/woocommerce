<?php

namespace Bliskapaczka\ApiClient;

use Psr\Log\LoggerInterface;

/**
 * Class Logger
 *
 * @package Bliskapaczka\ApiClient
 */
class Logger
{
    /**
     * @var  LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $message
     * @param array  $context
     */
    public function debug($message, array $context = array())
    {
        if ($this->logger) {
            $this->logger->debug($message, $context);
        }
    }

    /**
     * @param string $message
     * @param array  $context
     */
    public function error($message, array $context = array())
    {
        if ($this->logger) {
            $this->logger->error($message, $context);
        }
    }
}
