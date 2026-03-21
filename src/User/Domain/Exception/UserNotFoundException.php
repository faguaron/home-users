<?php

declare(strict_types=1);

namespace App\User\Domain\Exception;

final class UserNotFoundException extends \DomainException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('User with id "%s" not found.', $id));
    }
}
