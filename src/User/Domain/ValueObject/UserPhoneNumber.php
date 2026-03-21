<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

final class UserPhoneNumber
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = preg_replace('/\s+/', '', trim($value));

        if (null === $normalized) {
            throw new \InvalidArgumentException('Invalid phone number.');
        }

        // Accepts E.164 (+34XXXXXXXXX), Spanish local (9 digits starting with 6,7,8,9), or general international
        if (!preg_match('/^\+?[0-9]{7,15}$/', $normalized)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid phone number.', $value));
        }

        $this->value = $normalized;
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
