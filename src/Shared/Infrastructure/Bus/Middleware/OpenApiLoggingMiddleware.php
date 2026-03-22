<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Middleware;

use App\Shared\Application\Bus\Command\CommandInterface;
use App\Shared\Application\Bus\Query\QueryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class OpenApiLoggingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly LoggerInterface $apiLogger,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();
        $operation = $this->resolveOperation($message);
        $type = $message instanceof CommandInterface ? 'command' : 'query';
        $start = microtime(true);

        $entry = [
            'operation' => $operation,
            'type' => $type,
            'request' => get_object_vars($message),
        ];

        try {
            $result = $stack->next()->handle($envelope, $stack);

            $entry['duration_ms'] = round((microtime(true) - $start) * 1000, 2);
            $entry['status'] = 'success';

            if ($message instanceof QueryInterface) {
                $entry['response'] = $this->normalizeResult(
                    $result->last(HandledStamp::class)?->getResult(),
                );
            }

            $this->apiLogger->info(sprintf('%s %s', strtoupper($type), $operation), $entry);

            return $result;
        } catch (\Throwable $e) {
            $original = $e instanceof HandlerFailedException
                ? (current($e->getWrappedExceptions()) ?: $e)
                : $e;
            $entry['duration_ms'] = round((microtime(true) - $start) * 1000, 2);
            $entry['status'] = 'error';
            $entry['error'] = $original->getMessage();

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
