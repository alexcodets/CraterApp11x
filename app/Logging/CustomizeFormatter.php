<?php

namespace Crater\Logging;

use Illuminate\Log\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\IntrospectionProcessor;

//use Monolog\Logger;

class CustomizeFormatter
{
    protected string $dateFormat = 'Y-m-d H:i:s';
    public const SIMPLE_FORMAT = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";

    /**
     * Customize the given logger instance.
     *
     * @param Logger $logger
     * @return void
     */
    public function __invoke(Logger $logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor(new IntrospectionProcessor(\Monolog\Logger::DEBUG, ['Illuminate\\']));
            $handler->setFormatter(new LineFormatter(
                "[%datetime%] [%extra.class%, %extra.function%, %extra.line%] %channel%.%level_name%: %message% %context%\n",
                $this->dateFormat,
                true,
                true,
                true
            ));
        }

    }
}
