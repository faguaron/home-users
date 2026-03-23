<?php

declare(strict_types=1);

namespace App\User\Application\Delete;

use App\Shared\Application\Bus\Command\CommandInterface;
use App\User\Domain\ValueObject\UserId;

final class DeleteUserCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id,
    ) {
        new UserId($id);
    }
}
