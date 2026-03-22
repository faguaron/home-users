<?php

declare(strict_types=1);

namespace App\User\Application\Delete;

use App\Shared\Application\Bus\Command\CommandInterface;

final class DeleteUserCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id,
    ) {
    }
}
