<?php

declare(strict_types=1);

namespace App\User\Application\Update;

use App\User\Domain\Exception\UserAlreadyExistsException;
use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\ValueObject\UserBankAccountNumber;
use App\User\Domain\ValueObject\UserDateOfBirth;
use App\User\Domain\ValueObject\UserDni;
use App\User\Domain\ValueObject\UserEmail;
use App\User\Domain\ValueObject\UserFirstSurname;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use App\User\Domain\ValueObject\UserPhoneNumber;
use App\User\Domain\ValueObject\UserSecondSurname;

final class UpdateUserCommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(UpdateUserCommand $command): void
    {
        $id = new UserId($command->id);
        $user = $this->userRepository->findById($id);

        if (null === $user) {
            throw new UserNotFoundException($command->id);
        }

        $email = new UserEmail($command->email);
        $dni = new UserDni($command->dni);

        if ($this->userRepository->existsByEmailExcludingId($email, $id)) {
            throw new UserAlreadyExistsException('email', $command->email);
        }

        if ($this->userRepository->existsByDniExcludingId($dni, $id)) {
            throw new UserAlreadyExistsException('DNI', $command->dni);
        }

        $user->update(
            new UserName($command->name),
            new UserFirstSurname($command->firstSurname),
            new UserSecondSurname($command->secondSurname),
            $dni,
            $email,
            new UserPhoneNumber($command->phoneNumber),
            new UserBankAccountNumber($command->bankAccountNumber),
            UserDateOfBirth::fromString($command->dateOfBirth),
        );

        $this->userRepository->save($user);
    }
}
