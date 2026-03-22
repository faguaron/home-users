<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\Command\CommandBusInterface;
use App\Shared\Application\Bus\Command\CommandInterface;
use App\Shared\Infrastructure\Bus\Middleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;

final class CommandBus implements CommandBusInterface
{
    /** @param iterable<MiddlewareInterface> $middleware */
    public function __construct(
        private readonly iterable $middleware,
        private readonly ContainerInterface $handlers,
    ) {
    }

    public function dispatch(CommandInterface $command): void
    {
        $chain = $this->buildChain(iterator_to_array($this->middleware));
        $chain($command);
    }

    /** @param MiddlewareInterface[] $middleware */
    private function buildChain(array $middleware): callable
    {
        $handler = function (object $message): void {
            ($this->handlers->get($message::class))($message);
        };

        return array_reduce(
            array_reverse($middleware),
            fn (callable $next, MiddlewareInterface $mw) => fn (object $msg) => $mw->handle($msg, $next),
            $handler,
        );
    }
}
