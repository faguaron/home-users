<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Middleware;

use App\Shared\Application\Bus\Command\CommandInterface;
use App\Shared\Application\Bus\Query\QueryInterface;
use Psr\Log\LoggerInterface;

final class OpenApiLoggingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly LoggerInterface $apiLogger,
    ) {
    }

    public function handle(object $message, callable $next): mixed
    {
        $operation = $this->resolveOperation($message);
        $type = $message instanceof CommandInterface ? 'command' : 'query';
        $start = microtime(true);

        $entry = [
            'operation' => $operation,
            'type' => $type,
            'request' => get_object_vars($message),
        ];

        try {
            $result = $next($message);
            $entry['duration_ms'] = round((microtime(true) - $start) * 1000, 2);
            $entry['status'] = 'success';

            if ($message instanceof QueryInterface) {
                $entry['response'] = $this->normalizeResult($result);
            }

            $this->apiLogger->info(sprintf('%s %s', strtoupper($type), $operation), $entry);

            return $result;
        } catch (\Throwable $e) {
            $entry['duration_ms'] = round((microtime(true) - $start) * 1000, 2);
            $entry['status'] = 'error';
            $entry['error'] = $e->getMessage();

            $this->apiLogger->error(sprintf('%s %s', strtoupper($type), $operation), $entry);

            throw $e;
        }
    }

    private function resolveOperation(object $message): string
    {
        $parts = explode('\\', $message::class);
        $className = end($parts);

        return str_replace(['Command', 'Query'], '', $className);
    }

    private function normalizeResult(mixed $result): mixed
    {
        if ($result === null || is_scalar($result)) {
            return $result;
        }

        if (is_array($result)) {
            return array_map(
                fn (mixed $item) => is_object($item) ? $this->normalizeObject($item) : $item,
                $result,
            );
        }

        if (is_object($result)) {
            return $this->normalizeObject($result);
        }

        return null;
    }

    private function normalizeObject(object $object): mixed
    {
        if (method_exists($object, 'toPrimitives')) {
            return $object->toPrimitives();
        }

        $vars = get_object_vars($object);

        return $vars !== [] ? $vars : $object::class;
    }
}
