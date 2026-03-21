<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\ValueObject;

use App\User\Domain\ValueObject\UserBankAccountNumber;
use PHPUnit\Framework\TestCase;

final class UserBankAccountNumberTest extends TestCase
{
    public function testValidSpanishIbanIsAccepted(): void
    {
        // Valid Spanish IBAN
        $iban = new UserBankAccountNumber('ES9121000418450200051332');

        $this->assertSame('ES9121000418450200051332', $iban->value());
    }

    public function testIbanWithSpacesIsNormalized(): void
    {
        $iban = new UserBankAccountNumber('ES91 2100 0418 4502 0005 1332');

        $this->assertSame('ES9121000418450200051332', $iban->value());
    }

    public function testIbanIsNormalizedToUppercase(): void
    {
        $iban = new UserBankAccountNumber('es9121000418450200051332');

        $this->assertSame('ES9121000418450200051332', $iban->value());
    }

    public function testInvalidIbanThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/not a valid IBAN/');

        new UserBankAccountNumber('ES0000000000000000000000');
    }

    public function testInvalidFormatThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new UserBankAccountNumber('NOT-AN-IBAN');
    }

    public function testEquality(): void
    {
        $iban1 = new UserBankAccountNumber('ES9121000418450200051332');
        $iban2 = new UserBankAccountNumber('es9121000418450200051332');
        $iban3 = new UserBankAccountNumber('DE89370400440532013000');

        $this->assertTrue($iban1->equals($iban2));
        $this->assertFalse($iban1->equals($iban3));
    }
}
