<?php

declare(strict_types=1);

namespace App\User\Application\Update;

use App\Shared\Application\Bus\Command\CommandInterface;
use App\User\Domain\ValueObject\UserBankAccountNumber;
use App\User\Domain\ValueObject\UserDateOfBirth;
use App\User\Domain\ValueObject\UserDni;
use App\User\Domain\ValueObject\UserEmail;
use App\User\Domain\ValueObject\UserFirstSurname;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use App\User\Domain\ValueObject\UserPhoneNumber;
use App\User\Domain\ValueObject\UserSecondSurname;

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
        new UserId($id);
        new UserName($name);
        new UserFirstSurname($firstSurname);
        new UserSecondSurname($secondSurname);
        new UserDni($dni);
        new UserEmail($email);
        new UserPhoneNumber($phoneNumber);
        new UserBankAccountNumber($bankAccountNumber);
        UserDateOfBirth::fromString($dateOfBirth);
    }
}
