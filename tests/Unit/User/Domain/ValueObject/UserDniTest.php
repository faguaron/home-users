<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\ValueObject;

use App\User\Domain\ValueObject\UserDni;
use PHPUnit\Framework\TestCase;

final class UserDniTest extends TestCase
{
    public function testValidDniIsAccepted(): void
    {
        // 12345678Z is a valid DNI: 12345678 % 23 = 14 → 'Z'
        $dni = new UserDni('12345678Z');

        $this->assertSame('12345678Z', $dni->value());
    }

    public function testDniIsNormalizedToUppercase(): void
    {
        $dni = new UserDni('12345678z');

        $this->assertSame('12345678Z', $dni->value());
    }

    public function testInvalidFormatThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/not a valid Spanish DNI format/');

        new UserDni('1234567');
    }

    public function testInvalidControlLetterThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/invalid control letter/');

        // 12345678 % 23 = 14 → 'Z', so 'A' is wrong
        new UserDni('12345678A');
    }

    public function testLettersOnlyStringThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new UserDni('ABCDEFGHZ');
    }

    public function testEquality(): void
    {
        $dni1 = new UserDni('12345678Z');
        $dni2 = new UserDni('12345678z');
        $dni3 = new UserDni('00000023T');

        $this->assertTrue($dni1->equals($dni2));
        $this->assertFalse($dni1->equals($dni3));
    }
}
