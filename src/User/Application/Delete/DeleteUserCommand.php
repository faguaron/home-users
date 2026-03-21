<?php

declare(strict_types=1);

namespace App\User\Application\Delete;

final class DeleteUserCommand
{
    public function __construct(
        public readonly string $id,
    ) {
    }
}
