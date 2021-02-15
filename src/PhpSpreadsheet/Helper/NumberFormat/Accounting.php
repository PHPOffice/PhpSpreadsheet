<?php

namespace PhpOffice\PhpSpreadsheet\Helper\NumberFormat;

class Accounting extends Currency
{
    protected const FORMAT_STYLE = Number::FORMAT_STYLE_ACCOUNTING;

    protected $wrapNegativeValuesInBraces = false;

    protected $suppressSign = false;

    public function __construct(string $locale = 'en_US', ?string $currencyCode = null)
    {
        $this->setLocale($locale);
        $currencyCode = $this->setCurrencyCode($currencyCode);

        $mask = '#,##0.##';

        $formatterLocale = "{$this->locale}.UTF8@currency={$currencyCode}";
        $accountingFormatter = $this->internationalFormatter($formatterLocale);
        if ($accountingFormatter !== null) {
            $mask = $this->internationalCurrencySettings($accountingFormatter);
            $this->wrapNegativeValuesInBraces = strpos($mask, '(') !== false;
        }
        $this->baseNumberSettings($mask, $currencyCode);
    }

    public function wrapNegativeValues(bool $inBraces, bool $suppressNegativeSign = true): void
    {
        $this->wrapNegativeValuesInBraces = $inBraces;
        $this->suppressSign = $inBraces === false ? false : $suppressNegativeSign;
    }

    protected function setNegativeBracesMasking(string $maskSet): string
    {
        $masks = explode(self::MASK_SEPARATOR, $maskSet);

        $negativeMask = (array_key_exists(self::MASK_NEGATIVE_VALUE, $masks))
            ? $masks[self::MASK_NEGATIVE_VALUE]
            : $masks[self::MASK_POSITIVE_VALUE];
        $negativeMask = str_replace(['(', ')'], '', $negativeMask);
        $masks[self::MASK_NEGATIVE_VALUE] = "({$negativeMask})";

        ksort($masks);

        return implode(self::MASK_SEPARATOR, $masks);
    }

    protected function zeroValueMask(): string
    {
        return ($this->decimals === 0)
            ? '"-"'
            : '"-"' . str_repeat(self::DIGIT_POSITIONAL, $this->decimals);
    }

    protected function useIntlFormatMask(): string
    {
        $mask = parent::useIntlFormatMask();

        if ($this->wrapNegativeValuesInBraces === true) {
            $mask = $this->setNegativeBracesMasking($mask);
        }

        return $mask;
    }

    protected function buildMask(): string
    {
        $mask = Number::format();
        $mask = $this->setSymbolMask($mask);

        if ($this->wrapNegativeValuesInBraces === true) {
            $mask = $this->setNegativeBracesMasking($mask);
        }

        return $mask;
    }
}
