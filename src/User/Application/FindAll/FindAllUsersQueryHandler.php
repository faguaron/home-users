<?php

declare(strict_types=1);

namespace App\User\Application\FindAll;

use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\User;

final class FindAllUsersQueryHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /** @return User[] */
    public function __invoke(FindAllUsersQuery $query): array
    {
        return $this->userRepository->findAll();
    }
}
