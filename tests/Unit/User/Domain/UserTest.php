<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain;

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

final class UserTest extends TestCase
{
    public function testCreateSetsAllFieldsCorrectly(): void
    {
        $user = $this->buildUser();

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $user->id()->value());
        $this->assertSame('Joan', $user->name()->value());
        $this->assertSame('Garcia', $user->firstSurname()->value());
        $this->assertSame('Pérez', $user->secondSurname()->value());
        $this->assertSame('12345678Z', $user->dni()->value());
        $this->assertSame('joan.garcia@example.com', $user->email()->value());
        $this->assertSame('612345678', $user->phoneNumber()->value());
        $this->assertSame('ES9121000418450200051332', $user->bankAccountNumber()->value());
        $this->assertSame('1990-06-15', $user->dateOfBirth()->toString());
    }

    public function testCreateSetsCreatedAtAndUpdatedAt(): void
    {
        $before = new \DateTimeImmutable();
        $user = $this->buildUser();
        $after = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $user->createdAt());
        $this->assertLessThanOrEqual($after, $user->createdAt());
        $this->assertEquals($user->createdAt(), $user->updatedAt());
    }

    public function testUpdateChangesFieldsAndBumpsUpdatedAt(): void
    {
        $user = $this->buildUser();
        $originalUpdatedAt = $user->updatedAt();

        // Tiny sleep to ensure timestamp differs
        usleep(1000);

        $user->update(
            new UserName('Maria'),
            new UserFirstSurname('Martínez'),
            new UserSecondSurname('López'),
            new UserDni('00000023T'),
            new UserEmail('maria@example.com'),
            new UserPhoneNumber('699999999'),
            new UserBankAccountNumber('DE89370400440532013000'),
            UserDateOfBirth::fromString('1985-03-20'),
        );

        $this->assertSame('Maria', $user->name()->value());
        $this->assertSame('Martínez', $user->firstSurname()->value());
        $this->assertSame('López', $user->secondSurname()->value());
        $this->assertSame('00000023T', $user->dni()->value());
        $this->assertSame('maria@example.com', $user->email()->value());
        $this->assertSame('699999999', $user->phoneNumber()->value());
        $this->assertSame('DE89370400440532013000', $user->bankAccountNumber()->value());
        $this->assertSame('1985-03-20', $user->dateOfBirth()->toString());
        $this->assertGreaterThan($originalUpdatedAt, $user->updatedAt());
    }

    public function testToPrimitivesReturnsAllFields(): void
    {
        $user = $this->buildUser();
        $primitives = $user->toPrimitives();

        $this->assertArrayHasKey('id', $primitives);
        $this->assertArrayHasKey('name', $primitives);
        $this->assertArrayHasKey('first_surname', $primitives);
        $this->assertArrayHasKey('second_surname', $primitives);
        $this->assertArrayHasKey('dni', $primitives);
        $this->assertArrayHasKey('email', $primitives);
        $this->assertArrayHasKey('phone_number', $primitives);
        $this->assertArrayHasKey('bank_account_number', $primitives);
        $this->assertArrayHasKey('date_of_birth', $primitives);
        $this->assertArrayHasKey('created_at', $primitives);
        $this->assertArrayHasKey('updated_at', $primitives);

        $this->assertSame('Joan', $primitives['name']);
        $this->assertSame('joan.garcia@example.com', $primitives['email']);
    }

    public function testFromPrimitivesReconstitutesUser(): void
    {
        $user = User::fromPrimitives(
            '550e8400-e29b-41d4-a716-446655440000',
            'Joan',
            'Garcia',
            'Pérez',
            '12345678Z',
            'joan.garcia@example.com',
            '612345678',
            'ES9121000418450200051332',
            '1990-06-15',
            new \DateTimeImmutable('2024-01-01T10:00:00+00:00'),
            new \DateTimeImmutable('2024-01-02T12:00:00+00:00'),
        );

        $this->assertSame('Joan', $user->name()->value());
        $this->assertSame('2024-01-01', $user->createdAt()->format('Y-m-d'));
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
