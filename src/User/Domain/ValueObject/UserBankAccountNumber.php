<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

final class UserBankAccountNumber
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = strtoupper(preg_replace('/\s+/', '', trim($value)) ?? '');

        if (!$this->isValidIban($normalized)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid IBAN bank account number.', $value));
        }

        $this->value = $normalized;
    }

    private function isValidIban(string $iban): bool
    {
        if (!preg_match('/^[A-Z]{2}\d{2}[A-Z0-9]{1,30}$/', $iban)) {
            return false;
        }

        // Move first 4 chars to end, convert letters to numbers, check mod 97
        $rearranged = substr($iban, 4).substr($iban, 0, 4);
        $numeric = '';

        foreach (str_split($rearranged) as $char) {
            $numeric .= ctype_alpha($char) ? (string) (ord($char) - 55) : $char;
        }

        return 1 === $this->mod97($numeric);
    }

    private function mod97(string $numeric): int
    {
        $remainder = 0;

        foreach (str_split($numeric, 7) as $chunk) {
            $remainder = (int) ($remainder.$chunk) % 97;
        }

        return $remainder;
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
