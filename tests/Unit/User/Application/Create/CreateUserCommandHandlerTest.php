<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\Create;

use App\User\Application\Create\CreateUserCommand;
use App\User\Application\Create\CreateUserCommandHandler;
use App\User\Domain\Exception\UserAlreadyExistsException;
use App\User\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class CreateUserCommandHandlerTest extends TestCase
{
    public function testItCreatesAndSavesAUser(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository->expects($this->once())->method('existsByEmail')->willReturn(false);
        $repository->expects($this->once())->method('existsByDni')->willReturn(false);
        $repository->expects($this->once())->method('save');

        (new CreateUserCommandHandler($repository))($this->buildCommand());
    }

    public function testItThrowsWhenEmailAlreadyExists(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $repository = $this->createStub(UserRepositoryInterface::class);
        $repository->method('existsByEmail')->willReturn(true);

        (new CreateUserCommandHandler($repository))($this->buildCommand());
    }

    public function testItThrowsWhenDniAlreadyExists(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $repository = $this->createStub(UserRepositoryInterface::class);
        $repository->method('existsByEmail')->willReturn(false);
        $repository->method('existsByDni')->willReturn(true);

        (new CreateUserCommandHandler($repository))($this->buildCommand());
    }

    private function buildCommand(): CreateUserCommand
    {
        return new CreateUserCommand(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'Joan',
            firstSurname: 'Garcia',
            secondSurname: 'Pérez',
            dni: '12345678Z',
            email: 'joan.garcia@example.com',
            phoneNumber: '612345678',
            bankAccountNumber: 'ES9121000418450200051332',
            dateOfBirth: '1990-06-15',
        );
    }
}
