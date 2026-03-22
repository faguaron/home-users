<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Middleware;

interface MiddlewareInterface
{
    public function handle(object $message, callable $next): mixed;
}
