<?php

declare(strict_types=1);

namespace App\User\Domain\Repository;

use App\User\Domain\User;
use App\User\Domain\ValueObject\UserDni;
use App\User\Domain\ValueObject\UserEmail;
use App\User\Domain\ValueObject\UserId;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function findById(UserId $id): ?User;

    /** @return User[] */
    public function findAll(): array;

    public function delete(User $user): void;

    public function existsByEmail(UserEmail $email): bool;

    public function existsByEmailExcludingId(UserEmail $email, UserId $id): bool;

    public function existsByDni(UserDni $dni): bool;

    public function existsByDniExcludingId(UserDni $dni, UserId $id): bool;
}
