<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Doctrine;

class UserDoctrineEntity
{
    public string $id;
    public string $name;
    public string $firstSurname;
    public string $secondSurname;
    public string $dni;
    public string $email;
    public string $phoneNumber;
    public string $bankAccountNumber;
    public \DateTimeImmutable $dateOfBirth;
    public \DateTimeImmutable $createdAt;
    public \DateTimeImmutable $updatedAt;
}
