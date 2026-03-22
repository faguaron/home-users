<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\Query\QueryBusInterface;
use App\Shared\Application\Bus\Query\QueryInterface;
use App\Shared\Infrastructure\Bus\Middleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;

final class QueryBus implements QueryBusInterface
{
    /** @param iterable<MiddlewareInterface> $middleware */
    public function __construct(
        private readonly iterable $middleware,
        private readonly ContainerInterface $handlers,
    ) {
    }

    public function dispatch(QueryInterface $query): mixed
    {
        $chain = $this->buildChain(iterator_to_array($this->middleware));

        return $chain($query);
    }

    /** @param MiddlewareInterface[] $middleware */
    private function buildChain(array $middleware): callable
    {
        $handler = function (object $message): mixed {
            return ($this->handlers->get($message::class))($message);
        };

        return array_reduce(
            array_reverse($middleware),
            fn (callable $next, MiddlewareInterface $mw) => fn (object $msg) => $mw->handle($msg, $next),
            $handler,
        );
    }
}
