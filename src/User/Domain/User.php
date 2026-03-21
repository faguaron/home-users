<?php

declare(strict_types=1);

namespace App\User\Domain;

use App\User\Domain\ValueObject\UserBankAccountNumber;
use App\User\Domain\ValueObject\UserDateOfBirth;
use App\User\Domain\ValueObject\UserDni;
use App\User\Domain\ValueObject\UserEmail;
use App\User\Domain\ValueObject\UserFirstSurname;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use App\User\Domain\ValueObject\UserPhoneNumber;
use App\User\Domain\ValueObject\UserSecondSurname;

class User
{
    private UserId $id;
    private UserName $name;
    private UserFirstSurname $firstSurname;
    private UserSecondSurname $secondSurname;
    private UserDni $dni;
    private UserEmail $email;
    private UserPhoneNumber $phoneNumber;
    private UserBankAccountNumber $bankAccountNumber;
    private UserDateOfBirth $dateOfBirth;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    private function __construct(
        UserId $id,
        UserName $name,
        UserFirstSurname $firstSurname,
        UserSecondSurname $secondSurname,
        UserDni $dni,
        UserEmail $email,
        UserPhoneNumber $phoneNumber,
        UserBankAccountNumber $bankAccountNumber,
        UserDateOfBirth $dateOfBirth,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->firstSurname = $firstSurname;
        $this->secondSurname = $secondSurname;
        $this->dni = $dni;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->bankAccountNumber = $bankAccountNumber;
        $this->dateOfBirth = $dateOfBirth;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function create(
        UserId $id,
        UserName $name,
        UserFirstSurname $firstSurname,
        UserSecondSurname $secondSurname,
        UserDni $dni,
        UserEmail $email,
        UserPhoneNumber $phoneNumber,
        UserBankAccountNumber $bankAccountNumber,
        UserDateOfBirth $dateOfBirth,
    ): self {
        $now = new \DateTimeImmutable();

        return new self(
            $id,
            $name,
            $firstSurname,
            $secondSurname,
            $dni,
            $email,
            $phoneNumber,
            $bankAccountNumber,
            $dateOfBirth,
            $now,
            $now,
        );
    }

    public static function fromPrimitives(
        string $id,
        string $name,
        string $firstSurname,
        string $secondSurname,
        string $dni,
        string $email,
        string $phoneNumber,
        string $bankAccountNumber,
        string $dateOfBirth,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            new UserId($id),
            new UserName($name),
            new UserFirstSurname($firstSurname),
            new UserSecondSurname($secondSurname),
            new UserDni($dni),
            new UserEmail($email),
            new UserPhoneNumber($phoneNumber),
            new UserBankAccountNumber($bankAccountNumber),
            UserDateOfBirth::fromString($dateOfBirth),
            $createdAt,
            $updatedAt,
        );
    }

    public function update(
        UserName $name,
        UserFirstSurname $firstSurname,
        UserSecondSurname $secondSurname,
        UserDni $dni,
        UserEmail $email,
        UserPhoneNumber $phoneNumber,
        UserBankAccountNumber $bankAccountNumber,
        UserDateOfBirth $dateOfBirth,
    ): void {
        $this->name = $name;
        $this->firstSurname = $firstSurname;
        $this->secondSurname = $secondSurname;
        $this->dni = $dni;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->bankAccountNumber = $bankAccountNumber;
        $this->dateOfBirth = $dateOfBirth;
        $this->updatedAt = new \DateTimeImmutable();
    }

    /** @return array<string, mixed> */
    public function toPrimitives(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'first_surname' => $this->firstSurname->value(),
            'second_surname' => $this->secondSurname->value(),
            'dni' => $this->dni->value(),
            'email' => $this->email->value(),
            'phone_number' => $this->phoneNumber->value(),
            'bank_account_number' => $this->bankAccountNumber->value(),
            'date_of_birth' => $this->dateOfBirth->toString(),
            'created_at' => $this->createdAt->format(\DateTimeInterface::ATOM),
            'updated_at' => $this->updatedAt->format(\DateTimeInterface::ATOM),
        ];
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): UserName
    {
        return $this->name;
    }

    public function firstSurname(): UserFirstSurname
    {
        return $this->firstSurname;
    }

    public function secondSurname(): UserSecondSurname
    {
        return $this->secondSurname;
    }

    public function dni(): UserDni
    {
        return $this->dni;
    }

    public function email(): UserEmail
    {
        return $this->email;
    }

    public function phoneNumber(): UserPhoneNumber
    {
        return $this->phoneNumber;
    }

    public function bankAccountNumber(): UserBankAccountNumber
    {
        return $this->bankAccountNumber;
    }

    public function dateOfBirth(): UserDateOfBirth
    {
        return $this->dateOfBirth;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
