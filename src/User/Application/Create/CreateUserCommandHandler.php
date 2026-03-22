<?php

declare(strict_types=1);

namespace App\User\Application\Create;

use App\User\Domain\Exception\UserAlreadyExistsException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\User\Domain\Repository\UserRepositoryInterface;

use App\User\Domain\User;
use App\User\Domain\ValueObject\UserBankAccountNumber;
use App\User\Domain\ValueObject\UserDateOfBirth;
use App\User\Domain\ValueObject\UserDni;
use App\User\Domain\ValueObject\UserEmail;
use App\User\Domain\ValueObject\UserFirstSurname;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use App\User\Domain\ValueObject\UserPhoneNumber;
use App\User\Domain\ValueObject\UserSecondSurname;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateUserCommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $email = new UserEmail($command->email);
        $dni = new UserDni($command->dni);

        if ($this->userRepository->existsByEmail($email)) {
            throw new UserAlreadyExistsException('email', $command->email);
        }

        if ($this->userRepository->existsByDni($dni)) {
            throw new UserAlreadyExistsException('DNI', $command->dni);
        }

        $user = User::create(
            new UserId($command->id),
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
