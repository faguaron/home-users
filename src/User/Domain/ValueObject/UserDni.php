<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

final class UserDni
{
    private const LETTER_MAP = 'TRWAGMYFPDXBNJZSQVHLCKE';

    private string $value;

    public function __construct(string $value)
    {
        $normalized = strtoupper(trim($value));

        if (!preg_match('/^\d{8}[A-Z]$/', $normalized)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid Spanish DNI format (8 digits + letter).', $value));
        }

        $number = (int) substr($normalized, 0, 8);
        $letter = $normalized[8];
        $expectedLetter = self::LETTER_MAP[$number % 23];

        if ($letter !== $expectedLetter) {
            throw new \InvalidArgumentException(sprintf('DNI "%s" has an invalid control letter. Expected "%s".', $value, $expectedLetter));
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
