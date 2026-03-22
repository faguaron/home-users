<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\Query\QueryBusInterface;
use App\Shared\Application\Bus\Query\QueryInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class QueryBus implements QueryBusInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function dispatch(QueryInterface $query): mixed
    {
        try {
            $envelope = $this->messageBus->dispatch($query);
        } catch (HandlerFailedException $e) {
            throw current($e->getWrappedExceptions()) ?: $e;
        }

        return $envelope->last(HandledStamp::class)?->getResult();
    }
}
