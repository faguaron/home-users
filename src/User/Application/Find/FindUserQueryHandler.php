<?php

declare(strict_types=1);

namespace App\User\Application\Find;

use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\UserId;

final class FindUserQueryHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(FindUserQuery $query): User
    {
        $id = new UserId($query->id);
        $user = $this->userRepository->findById($id);

        if (null === $user) {
            throw new UserNotFoundException($query->id);
        }

        return $user;
    }
}
