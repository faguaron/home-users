<?php

declare(strict_types=1);

namespace App\User\Application\Create;

final class CreateUserCommand
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
