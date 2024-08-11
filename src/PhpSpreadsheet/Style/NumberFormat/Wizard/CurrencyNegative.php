<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard;

enum CurrencyNegative
{
    // PHPCompatibility (versions check) has an error which
    // causes it to flag the use of $this below as erroneous.
    // They fixed it in their development branch in October 2022.
    // But they haven't had a release since 2019!
    // Hence the phpcs disable statment below.
    case minus;
    case redMinus;
    case parentheses;
    case redParentheses;

    // phpcs:disable PHPCompatibility.Variables.ForbiddenThisUseContexts
    public function start(): string
    {
        return match ($this) {
            self::minus, self::redMinus => '-',
            self::parentheses, self::redParentheses => '\\(',
        };
    }

    public function end(): string
    {
        return match ($this) {
            self::minus, self::redMinus => '',
            self::parentheses, self::redParentheses => '\\)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::redParentheses, self::redMinus => '[Red]',
            self::parentheses, self::minus => '',
        };
    }
    // phpcs:enable
}
