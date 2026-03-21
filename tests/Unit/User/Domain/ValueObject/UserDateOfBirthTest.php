<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\ValueObject;

use App\User\Domain\ValueObject\UserDateOfBirth;
use PHPUnit\Framework\TestCase;

final class UserDateOfBirthTest extends TestCase
{
    public function testValidPastDateIsAccepted(): void
    {
        $dob = UserDateOfBirth::fromString('1990-06-15');

        $this->assertSame('1990-06-15', $dob->toString());
    }

    public function testFutureDateThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/cannot be in the future/');

        $future = new \DateTimeImmutable('+1 year');
        new UserDateOfBirth($future);
    }

    public function testInvalidStringFormatThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/not a valid date/');

        UserDateOfBirth::fromString('15/06/1990');
    }

    public function testTodayIsAccepted(): void
    {
        $today = new \DateTimeImmutable('today');
        $dob = new UserDateOfBirth($today);

        $this->assertSame($today->format('Y-m-d'), $dob->toString());
    }

    public function testEquality(): void
    {
        $dob1 = UserDateOfBirth::fromString('1990-06-15');
        $dob2 = UserDateOfBirth::fromString('1990-06-15');
        $dob3 = UserDateOfBirth::fromString('1995-01-01');

        $this->assertTrue($dob1->equals($dob2));
        $this->assertFalse($dob1->equals($dob3));
    }
}
