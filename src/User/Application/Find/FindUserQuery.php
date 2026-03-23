<?php

declare(strict_types=1);

namespace App\User\Application\Find;

use App\Shared\Application\Bus\Query\QueryInterface;
use App\User\Domain\ValueObject\UserId;

final class FindUserQuery implements QueryInterface
{
    public function __construct(
        public readonly string $id,
    ) {
        new UserId($id);
    }
}
