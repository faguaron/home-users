<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\Command\CommandBusInterface;
use App\Shared\Application\Bus\Command\CommandInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final class CommandBus implements CommandBusInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function dispatch(CommandInterface $command): void
    {
        try {
            $this->messageBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            throw current($e->getWrappedExceptions()) ?: $e;
        }
    }
}
