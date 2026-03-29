<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Schema;

use Doctrine\DBAL\Connection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SqliteSchemaInitializer implements EventSubscriberInterface
{
    private bool $initialized = false;

    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9999],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->initialized || !$event->isMainRequest()) {
            return;
        }

        $this->connection->executeStatement('
            CREATE TABLE IF NOT EXISTS users (
                id VARCHAR(36) NOT NULL PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                first_surname VARCHAR(100) NOT NULL,
                second_surname VARCHAR(100) NOT NULL,
                dni VARCHAR(9) NOT NULL UNIQUE,
                email VARCHAR(255) NOT NULL UNIQUE,
                phone_number VARCHAR(20) NOT NULL,
                bank_account_number VARCHAR(34) NOT NULL,
                date_of_birth DATE NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            )
        ');

        $this->initialized = true;
    }
}
