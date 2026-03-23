<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\Find;

use App\User\Application\Find\FindUserQuery;
use PHPUnit\Framework\TestCase;

final class FindUserQueryTest extends TestCase
{
    public function testItCanBeConstructedWithValidId(): void
    {
        $query = new FindUserQuery(id: '550e8400-e29b-41d4-a716-446655440000');

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $query->id);
    }

    public function testItThrowsOnInvalidId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new FindUserQuery(id: 'not-a-uuid');
    }
}
