<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Persistence;

use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\UserDni;
use App\User\Domain\ValueObject\UserEmail;
use App\User\Domain\ValueObject\UserId;
use Doctrine\DBAL\Connection;

final class DbalUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function save(User $user): void
    {
        $primitives = $user->toPrimitives();

        $exists = (bool) $this->connection->fetchOne(
            'SELECT 1 FROM users WHERE id = :id',
            ['id' => $primitives['id']]
        );

        if ($exists) {
            $this->connection->update('users', [
                'name' => $primitives['name'],
                'first_surname' => $primitives['first_surname'],
                'second_surname' => $primitives['second_surname'],
                'dni' => $primitives['dni'],
                'email' => $primitives['email'],
                'phone_number' => $primitives['phone_number'],
                'bank_account_number' => $primitives['bank_account_number'],
                'date_of_birth' => $primitives['date_of_birth'],
                'created_at' => $primitives['created_at'],
                'updated_at' => $primitives['updated_at'],
            ], ['id' => $primitives['id']]);
        } else {
            $this->connection->insert('users', [
                'id' => $primitives['id'],
                'name' => $primitives['name'],
                'first_surname' => $primitives['first_surname'],
                'second_surname' => $primitives['second_surname'],
                'dni' => $primitives['dni'],
                'email' => $primitives['email'],
                'phone_number' => $primitives['phone_number'],
                'bank_account_number' => $primitives['bank_account_number'],
                'date_of_birth' => $primitives['date_of_birth'],
                'created_at' => $primitives['created_at'],
                'updated_at' => $primitives['updated_at'],
            ]);
        }
    }

    public function findById(UserId $id): ?User
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM users WHERE id = :id',
            ['id' => $id->value()]
        );

        if ($row === false) {
            return null;
        }

        return $this->toDomain($row);
    }

    public function findAll(): array
    {
        $rows = $this->connection->fetchAllAssociative('SELECT * FROM users');

        return array_map(fn (array $row) => $this->toDomain($row), $rows);
    }

    public function delete(User $user): void
    {
        $this->connection->delete('users', ['id' => $user->id()->value()]);
    }

    public function existsByEmail(UserEmail $email): bool
    {
        return (bool) $this->connection->fetchOne(
            'SELECT 1 FROM users WHERE email = :email',
            ['email' => $email->value()]
        );
    }

    public function existsByEmailExcludingId(UserEmail $email, UserId $id): bool
    {
        return (bool) $this->connection->fetchOne(
            'SELECT 1 FROM users WHERE email = :email AND id != :id',
            ['email' => $email->value(), 'id' => $id->value()]
        );
    }

    public function existsByDni(UserDni $dni): bool
    {
        return (bool) $this->connection->fetchOne(
            'SELECT 1 FROM users WHERE dni = :dni',
            ['dni' => $dni->value()]
        );
    }

    public function existsByDniExcludingId(UserDni $dni, UserId $id): bool
    {
        return (bool) $this->connection->fetchOne(
            'SELECT 1 FROM users WHERE dni = :dni AND id != :id',
            ['dni' => $dni->value(), 'id' => $id->value()]
        );
    }

    /** @param array<string, mixed> $row */
    private function toDomain(array $row): User
    {
        return User::fromPrimitives(
            (string) $row['id'],
            (string) $row['name'],
            (string) $row['first_surname'],
            (string) $row['second_surname'],
            (string) $row['dni'],
            (string) $row['email'],
            (string) $row['phone_number'],
            (string) $row['bank_account_number'],
            (string) $row['date_of_birth'],
            new \DateTimeImmutable((string) $row['created_at']),
            new \DateTimeImmutable((string) $row['updated_at']),
        );
    }
}
