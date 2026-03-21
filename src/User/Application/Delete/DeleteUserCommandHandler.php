<?php

declare(strict_types=1);

namespace App\User\Application\Delete;

use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\ValueObject\UserId;

final class DeleteUserCommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(DeleteUserCommand $command): void
    {
        $id = new UserId($command->id);
        $user = $this->userRepository->findById($id);

        if (null === $user) {
            throw new UserNotFoundException($command->id);
        }

        $this->userRepository->delete($user);
    }
}
