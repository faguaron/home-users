<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\Update;

use App\User\Application\Update\UpdateUserCommand;
use App\User\Application\Update\UpdateUserCommandHandler;
use App\User\Domain\Exception\UserAlreadyExistsException;
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

final class UpdateUserCommandHandlerTest extends TestCase
{
    public function testItUpdatesAndSavesUser(): void
    {
        $user = $this->buildUser();

        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository->method('findById')->willReturn($user);
        $repository->method('existsByEmailExcludingId')->willReturn(false);
        $repository->method('existsByDniExcludingId')->willReturn(false);
        $repository->expects($this->once())->method('save');

        (new UpdateUserCommandHandler($repository))($this->buildCommand());

        $this->assertSame('Maria', $user->name()->value());
    }

    public function testItThrowsWhenUserNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $repository = $this->createStub(UserRepositoryInterface::class);
        $repository->method('findById')->willReturn(null);

        (new UpdateUserCommandHandler($repository))($this->buildCommand());
    }

    public function testItThrowsWhenEmailTakenByAnotherUser(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $repository = $this->createStub(UserRepositoryInterface::class);
        $repository->method('findById')->willReturn($this->buildUser());
        $repository->method('existsByEmailExcludingId')->willReturn(true);

        (new UpdateUserCommandHandler($repository))($this->buildCommand());
    }

    public function testItThrowsWhenDniTakenByAnotherUser(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $repository = $this->createStub(UserRepositoryInterface::class);
        $repository->method('findById')->willReturn($this->buildUser());
        $repository->method('existsByEmailExcludingId')->willReturn(false);
        $repository->method('existsByDniExcludingId')->willReturn(true);

        (new UpdateUserCommandHandler($repository))($this->buildCommand());
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

    private function buildCommand(): UpdateUserCommand
    {
        return new UpdateUserCommand(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'Maria',
            firstSurname: 'Martínez',
            secondSurname: 'López',
            dni: '12345678Z',
            email: 'joan.garcia@example.com',
            phoneNumber: '612345678',
            bankAccountNumber: 'ES9121000418450200051332',
            dateOfBirth: '1990-06-15',
        );
    }
}
