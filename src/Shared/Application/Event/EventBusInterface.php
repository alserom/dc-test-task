<?php

declare(strict_types=1);

namespace App\Shared\Application\Event;

/**
 * TODO@technical-debt: Change transport to Kafka
 * The main idea was that events can be processed by many independent services. But I think it's not possible with RabbitMQ.
 * Or I don't know how to do this. So a better solution would be to change the transport for `event.bus` to Kafka.
 */
interface EventBusInterface
{
    public function dispatch(EventInterface $event): void;
}
