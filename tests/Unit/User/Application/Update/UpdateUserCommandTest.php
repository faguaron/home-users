<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\Update;

use App\User\Application\Update\UpdateUserCommand;
use PHPUnit\Framework\TestCase;

final class UpdateUserCommandTest extends TestCase
{
    public function testItCanBeConstructedWithValidData(): void
    {
        $command = new UpdateUserCommand(
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

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $command->id);
        $this->assertSame('Joan', $command->name);
    }

    public function testItThrowsOnInvalidId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new UpdateUserCommand(
            id: 'not-a-uuid',
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

    public function testItThrowsOnInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new UpdateUserCommand(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'Joan',
            firstSurname: 'Garcia',
            secondSurname: 'Pérez',
            dni: '12345678Z',
            email: 'not-an-email',
            phoneNumber: '612345678',
            bankAccountNumber: 'ES9121000418450200051332',
            dateOfBirth: '1990-06-15',
        );
    }

    public function testItThrowsOnInvalidDni(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new UpdateUserCommand(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'Joan',
            firstSurname: 'Garcia',
            secondSurname: 'Pérez',
            dni: 'INVALID',
            email: 'joan.garcia@example.com',
            phoneNumber: '612345678',
            bankAccountNumber: 'ES9121000418450200051332',
            dateOfBirth: '1990-06-15',
        );
    }
}
