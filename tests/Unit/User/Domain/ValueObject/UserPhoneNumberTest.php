<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\ValueObject;

use App\User\Domain\ValueObject\UserPhoneNumber;
use PHPUnit\Framework\TestCase;

final class UserPhoneNumberTest extends TestCase
{
    public function testValidSpanishPhoneIsAccepted(): void
    {
        $phone = new UserPhoneNumber('612345678');

        $this->assertSame('612345678', $phone->value());
    }

    public function testValidE164PhoneIsAccepted(): void
    {
        $phone = new UserPhoneNumber('+34612345678');

        $this->assertSame('+34612345678', $phone->value());
    }

    public function testPhoneWithSpacesIsNormalized(): void
    {
        $phone = new UserPhoneNumber('612 345 678');

        $this->assertSame('612345678', $phone->value());
    }

    public function testTooShortPhoneThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/not a valid phone number/');

        new UserPhoneNumber('123456');
    }

    public function testAlphabeticPhoneThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new UserPhoneNumber('ABCDEFGHI');
    }

    public function testEquality(): void
    {
        $phone1 = new UserPhoneNumber('612345678');
        $phone2 = new UserPhoneNumber('612 345 678');
        $phone3 = new UserPhoneNumber('699999999');

        $this->assertTrue($phone1->equals($phone2));
        $this->assertFalse($phone1->equals($phone3));
    }
}
