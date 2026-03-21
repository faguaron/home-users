<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

final class UserSecondSurname
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ('' === $trimmed) {
            throw new \InvalidArgumentException('User second surname cannot be empty.');
        }

        if (mb_strlen($trimmed) > 100) {
            throw new \InvalidArgumentException('User second surname cannot exceed 100 characters.');
        }

        $this->value = $trimmed;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
