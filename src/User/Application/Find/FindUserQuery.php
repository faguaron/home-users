<?php

declare(strict_types=1);

namespace App\User\Application\Find;

final class FindUserQuery
{
    public function __construct(
        public readonly string $id,
    ) {
    }
}
