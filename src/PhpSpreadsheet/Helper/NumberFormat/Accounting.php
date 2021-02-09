<?php

namespace PhpOffice\PhpSpreadsheet\Helper\NumberFormat;

use NumberFormatter;

class Accounting extends Currency
{
    protected const FORMAT_STYLE = Number::FORMAT_STYLE_ACCOUNTING;

    protected $wrapNegativeValuesInBraces = false;

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

    public function wrapNegativeValues(bool $inBraces): void
    {
        $this->wrapNegativeValuesInBraces = $inBraces;
    }

    protected function maskBraces(string $maskSet): string
    {
        $masks = explode(self::MASK_SEPARATOR, $maskSet);

        $negativeMask = (array_key_exists(self::MASK_NEGATIVE_VALUE, $masks))
            ? $masks[self::MASK_NEGATIVE_VALUE] : $masks[self::MASK_POSITIVE_VALUE];
        $masks[self::MASK_NEGATIVE_VALUE] = "({$negativeMask})";

        ksort($masks);

        return implode(self::MASK_SEPARATOR, $masks);
    }

    protected function maskSymbol(string $maskSet): string
    {
        // TODO Override of Separator between value and Currency Symbol
        $masks = explode(self::MASK_SEPARATOR, $maskSet);

        $paddedCurrencySymbol = ($this->leading === self::CURRENCY_SYMBOL_LEADING)
            ? $this->localeAwareCurrencySymbol() . $this->currencySeparator
            : $this->currencySeparator . $this->localeAwareCurrencySymbol();

        foreach ($masks as &$mask) {
            $mask = preg_replace(self::SYMBOL_PATTERN_MASK, '', $mask);
            $bracesRequired = $this->wrapNegativeValuesInBraces && strpos($mask, '(') !== false;
            $mask = ($bracesRequired) ? trim($mask, '()') : $mask;
            $mask = ($this->leading === self::CURRENCY_SYMBOL_LEADING)
                ? $paddedCurrencySymbol . $mask
                : $mask . $paddedCurrencySymbol;
            $mask = ($bracesRequired) ? "({$mask})" : $mask;
        }

        return implode(self::MASK_SEPARATOR, $masks);
    }

    protected function useIntlFormatMask(): string
    {
        $mask = $this->intlMask;
        $mask = $this->setThousandsInMask($mask);
        $mask = $this->setDecimalsInMask($mask);

        if ($this->wrapNegativeValuesInBraces === true) {
            $mask = $this->maskBraces($mask);
        } elseif ($this->trailingSign === true || $this->displayPositiveSign === true || $this->signSeparator !== '') {
            // Set positive/negative signs in the correct position, with the correct padding
            $mask = $this->maskSign($mask);
        }

        // Set the currency symbol in the required position, with required padding
        $mask = $this->maskSymbol($mask);

        return $mask;
    }

    protected function buildMask(): string
    {
        $maskSet = Number::format();

        $paddedCurrencySymbol = ($this->leading === self::CURRENCY_SYMBOL_LEADING)
            ? $this->localeAwareCurrencySymbol() . $this->currencySeparator
            : $this->currencySeparator . $this->localeAwareCurrencySymbol();
        $this->maskSign($maskSet);

        $masks = explode(self::MASK_SEPARATOR, $maskSet);

        if ($this->wrapNegativeValuesInBraces === true || $leading = self::CURRENCY_SYMBOL_TRAILING) {
            $negativeMask = (array_key_exists(self::MASK_NEGATIVE_VALUE, $masks))
                ? $masks[self::MASK_NEGATIVE_VALUE] : $masks[self::MASK_POSITIVE_VALUE];
            $masks[self::MASK_NEGATIVE_VALUE] = $this->wrapNegativeValuesInBraces
                ? "({$negativeMask})"
                : $negativeMask;
        }

        foreach ($masks as &$mask) {
            $bracesRequired = $this->wrapNegativeValuesInBraces === true && strpos($mask, '(') !== false;
            $mask = ($bracesRequired) ? trim($mask, '()') : $mask;
            $mask = ($this->leading === self::CURRENCY_SYMBOL_LEADING)
                ? $paddedCurrencySymbol . $mask
                : $mask . $paddedCurrencySymbol;
            $mask = ($bracesRequired) ? "({$mask})" : $mask;
        }

        return implode(self::MASK_SEPARATOR, $masks);
    }
}
