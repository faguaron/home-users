<?php

declare(strict_types=1);

namespace App\User\Application\Update;

use App\Shared\Application\Bus\Command\CommandInterface;

final class UpdateUserCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $firstSurname,
        public readonly string $secondSurname,
        public readonly string $dni,
        public readonly string $email,
        public readonly string $phoneNumber,
        public readonly string $bankAccountNumber,
        public readonly string $dateOfBirth,
    ) {
    }
}
