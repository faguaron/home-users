<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

final class UserDateOfBirth
{
    private \DateTimeImmutable $value;

    public function __construct(\DateTimeImmutable $value)
    {
        $today = new \DateTimeImmutable('today');

        if ($value > $today) {
            throw new \InvalidArgumentException('Date of birth cannot be in the future.');
        }

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $value);

        if (false === $date) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid date. Expected format: Y-m-d.', $value));
        }

        return new self($date->setTime(0, 0, 0));
    }

    public function value(): \DateTimeImmutable
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value->format('Y-m-d');
    }

    public function equals(self $other): bool
    {
        return $this->value == $other->value;
    }
}
