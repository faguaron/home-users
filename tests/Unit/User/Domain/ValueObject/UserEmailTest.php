<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\ValueObject;

use App\User\Domain\ValueObject\UserEmail;
use PHPUnit\Framework\TestCase;

final class UserEmailTest extends TestCase
{
    public function testValidEmailIsAccepted(): void
    {
        $email = new UserEmail('user@example.com');

        $this->assertSame('user@example.com', $email->value());
    }

    public function testEmailIsNormalizedToLowercase(): void
    {
        $email = new UserEmail('User@EXAMPLE.COM');

        $this->assertSame('user@example.com', $email->value());
    }

    public function testEmailWithLeadingTrailingSpacesIsNormalized(): void
    {
        $email = new UserEmail('  user@example.com  ');

        $this->assertSame('user@example.com', $email->value());
    }

    public function testInvalidEmailThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/not a valid email/');

        new UserEmail('not-an-email');
    }

    public function testEmptyEmailThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new UserEmail('');
    }

    public function testEquality(): void
    {
        $email1 = new UserEmail('user@example.com');
        $email2 = new UserEmail('USER@EXAMPLE.COM');
        $email3 = new UserEmail('other@example.com');

        $this->assertTrue($email1->equals($email2));
        $this->assertFalse($email1->equals($email3));
    }
}
