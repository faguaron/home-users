<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\Create;

use App\User\Application\Create\CreateUserCommand;
use PHPUnit\Framework\TestCase;

final class CreateUserCommandTest extends TestCase
{
    public function testItCanBeConstructedWithValidData(): void
    {
        $command = new CreateUserCommand(
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
        $this->assertSame('Garcia', $command->firstSurname);
        $this->assertSame('Pérez', $command->secondSurname);
        $this->assertSame('12345678Z', $command->dni);
        $this->assertSame('joan.garcia@example.com', $command->email);
        $this->assertSame('612345678', $command->phoneNumber);
        $this->assertSame('ES9121000418450200051332', $command->bankAccountNumber);
        $this->assertSame('1990-06-15', $command->dateOfBirth);
    }

    public function testItThrowsOnInvalidId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CreateUserCommand(
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

    public function testItThrowsOnEmptyName(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CreateUserCommand(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: '',
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

        new CreateUserCommand(
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

        new CreateUserCommand(
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

    public function testItThrowsOnInvalidBankAccountNumber(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CreateUserCommand(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'Joan',
            firstSurname: 'Garcia',
            secondSurname: 'Pérez',
            dni: '12345678Z',
            email: 'joan.garcia@example.com',
            phoneNumber: '612345678',
            bankAccountNumber: 'INVALID-IBAN',
            dateOfBirth: '1990-06-15',
        );
    }

    public function testItThrowsOnInvalidDateOfBirth(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CreateUserCommand(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'Joan',
            firstSurname: 'Garcia',
            secondSurname: 'Pérez',
            dni: '12345678Z',
            email: 'joan.garcia@example.com',
            phoneNumber: '612345678',
            bankAccountNumber: 'ES9121000418450200051332',
            dateOfBirth: 'not-a-date',
        );
    }
}
