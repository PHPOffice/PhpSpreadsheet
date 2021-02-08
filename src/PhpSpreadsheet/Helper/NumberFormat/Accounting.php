<?php

namespace PhpOffice\PhpSpreadsheet\Helper\NumberFormat;

use NumberFormatter;

class Accounting extends Currency
{
    protected $wrapNegativeValuesInBraces;

    public function __construct(string $locale = 'en_US', string $currencyCode = null)
    {
        $locale = new Locale($locale);
        $countryCode = $locale->getCountryCode() ?? 'US';
        $this->locale = $locale->getLocale();

        if ($currencyCode === null) {
            $currencyCode = CurrencyLookup::lookup($countryCode);
        }
        $this->currencyCode = $currencyCode;

        $mask = '#,##0.##';
        $locale = "{$locale}.UTF8@currency={$currencyCode}";
        $currencyFormatter = $this->internationalFormatter($locale, Number::FORMAT_STYLE_ACCOUNTING);
        if ($currencyFormatter !== null) {
            $this->currencySymbol = $currencyFormatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
            $this->intlMask = $currencyFormatter->getPattern();
            $this->wrapNegativeValuesInBraces = strpos($this->intlMask, '(') !== false;
            $this->identifySignPosition();
            $this->identifyCurrencySymbolPosition();
            $mask = $this->intlMask;
        }

        if ($this->currencySymbol === null || $this->currencySymbol === false) {
            $this->currencySymbol = CurrencySymbolLookup::lookup($currencyCode) ?? self::CURRENCYCODE_PLACEHOLDER;
        }

        $this->useThousandsSeparator(strpos($mask, ',') !== false);
        $decimalMatch = (bool) (preg_match('/\.[0|#]+/', $mask, $decimals));
        $this->setDecimals($decimalMatch ? strlen($decimals[0]) - 1 : 0);

        $this->mask = $mask;
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

        // Set requested number of decimal places
        $decimalReplacement = ($this->decimals === 0)
            ? '0'
            : '0' . self::DECIMAL_SEPARATOR . str_repeat('0', $this->decimals);
        $mask = preg_replace(self::DECIMAL_PATTERN_MASK, $decimalReplacement, $mask);

        if ($this->wrapNegativeValuesInBraces === true) {
            $this->maskBraces($mask);
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

        $masks = explode(self::MASK_SEPARATOR, $maskSet);
        foreach ($masks as &$mask) {
            $bracesRequired = $this->wrapNegativeValuesInBraces && strpos($mask, '(') !== false;
            $mask = ($bracesRequired) ? trim($mask, '()') : $mask;
            $mask = ($this->leading === self::CURRENCY_SYMBOL_LEADING)
                ? $paddedCurrencySymbol . $mask
                : $mask . $paddedCurrencySymbol;
            $mask = ($bracesRequired) ? "({$mask})" : $mask;
        }

        return implode(self::MASK_SEPARATOR, $masks);
    }
}
