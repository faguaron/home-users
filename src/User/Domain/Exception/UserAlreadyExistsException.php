<?php

declare(strict_types=1);

namespace App\User\Domain\Exception;

final class UserAlreadyExistsException extends \DomainException
{
    public function __construct(string $field, string $value)
    {
        parent::__construct(sprintf('A user with %s "%s" already exists.', $field, $value));
    }
}
