<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\Find;

use App\User\Application\Find\FindUserQuery;
use App\User\Application\Find\FindUserQueryHandler;
use App\User\Domain\Exception\UserNotFoundException;
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

final class FindUserQueryHandlerTest extends TestCase
{
    public function testItReturnsUserWhenFound(): void
    {
        $user = $this->buildUser();

        $repository = $this->createStub(UserRepositoryInterface::class);
        $repository->method('findById')->willReturn($user);

        $result = (new FindUserQueryHandler($repository))(new FindUserQuery('550e8400-e29b-41d4-a716-446655440000'));

        $this->assertSame($user, $result);
    }

    public function testItThrowsWhenUserNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $repository = $this->createStub(UserRepositoryInterface::class);
        $repository->method('findById')->willReturn(null);

        (new FindUserQueryHandler($repository))(new FindUserQuery('550e8400-e29b-41d4-a716-446655440000'));
    }

    private function buildUser(): User
    {
        return User::create(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new UserName('Joan'),
            new UserFirstSurname('Garcia'),
            new UserSecondSurname('Pérez'),
            new UserDni('12345678Z'),
            new UserEmail('joan.garcia@example.com'),
            new UserPhoneNumber('612345678'),
            new UserBankAccountNumber('ES9121000418450200051332'),
            UserDateOfBirth::fromString('1990-06-15'),
        );
    }
}
