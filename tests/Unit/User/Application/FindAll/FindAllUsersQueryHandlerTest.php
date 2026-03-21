<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\FindAll;

use App\User\Application\FindAll\FindAllUsersQuery;
use App\User\Application\FindAll\FindAllUsersQueryHandler;
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
use PHPUnit\Framework\TestCase;

final class FindAllUsersQueryHandlerTest extends TestCase
{
    public function testItReturnsAllUsers(): void
    {
        $users = [$this->buildUser('550e8400-e29b-41d4-a716-446655440000'), $this->buildUser('550e8400-e29b-41d4-a716-446655440001')];

        $repository = $this->createStub(UserRepositoryInterface::class);
        $repository->method('findAll')->willReturn($users);

        $result = (new FindAllUsersQueryHandler($repository))(new FindAllUsersQuery());

        $this->assertCount(2, $result);
        $this->assertSame($users, $result);
    }

    public function testItReturnsEmptyArrayWhenNoUsers(): void
    {
        $repository = $this->createStub(UserRepositoryInterface::class);
        $repository->method('findAll')->willReturn([]);

        $result = (new FindAllUsersQueryHandler($repository))(new FindAllUsersQuery());

        $this->assertSame([], $result);
    }

    private function buildUser(string $id): User
    {
        return User::create(
            new UserId($id),
            new UserName('Joan'),
            new UserFirstSurname('Garcia'),
            new UserSecondSurname('Pérez'),
            new UserDni('12345678Z'),
            new UserEmail('joan.'.$id.'@example.com'),
            new UserPhoneNumber('612345678'),
            new UserBankAccountNumber('ES9121000418450200051332'),
            UserDateOfBirth::fromString('1990-06-15'),
        );
    }
}
