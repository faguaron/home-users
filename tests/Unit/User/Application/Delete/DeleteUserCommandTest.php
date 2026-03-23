<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\Delete;

use App\User\Application\Delete\DeleteUserCommand;
use PHPUnit\Framework\TestCase;

final class DeleteUserCommandTest extends TestCase
{
    public function testItCanBeConstructedWithValidId(): void
    {
        $command = new DeleteUserCommand(id: '550e8400-e29b-41d4-a716-446655440000');

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $command->id);
    }

    public function testItThrowsOnInvalidId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new DeleteUserCommand(id: 'not-a-uuid');
    }
}
