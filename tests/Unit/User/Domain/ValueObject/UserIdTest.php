<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\ValueObject;

use App\User\Domain\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

final class UserIdTest extends TestCase
{
    public function testValidUuidIsAccepted(): void
    {
        $id = new UserId('550e8400-e29b-41d4-a716-446655440000');

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $id->value());
    }

    public function testInvalidUuidThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/not a valid UUID/');

        new UserId('not-a-uuid');
    }

    public function testGenerateCreatesValidUuid(): void
    {
        $id = UserId::generate();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $id->value()
        );
    }

    public function testEquality(): void
    {
        $id1 = new UserId('550e8400-e29b-41d4-a716-446655440000');
        $id2 = new UserId('550e8400-e29b-41d4-a716-446655440000');
        $id3 = UserId::generate();

        $this->assertTrue($id1->equals($id2));
        $this->assertFalse($id1->equals($id3));
    }
}
