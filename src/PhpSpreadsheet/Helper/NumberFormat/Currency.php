<?php

namespace PhpOffice\PhpSpreadsheet\Helper\NumberFormat;

use NumberFormatter;

class Currency extends Number
{
    protected const FORMAT_STYLE = Number::FORMAT_STYLE_CURRENCY;

    public const CURRENCYCODE_PLACEHOLDER = '¤';

    public const CURRENCY_SYMBOL_LEADING = true;
    public const CURRENCY_SYMBOL_TRAILING = false;

    protected $currencyCode;

    protected $currencySymbol;

    protected $currencySeparator = '';

    protected $leading = self::CURRENCY_SYMBOL_LEADING;

    public function __construct(string $locale = 'en_US', ?string $currencyCode = null)
    {
        $countryCode = $this->setLocale($locale)->getCountryCode();
        $currencyCode = $this->setCurrencyCode($currencyCode);

        $mask = in_array($countryCode, self::LAKH_COUNTRIES, true) ? '#,##,##0.##' : '#,##0.##';

        $formatterLocale = "{$this->locale}.UTF8@currency={$currencyCode}";
        $currencyFormatter = $this->internationalFormatter($formatterLocale);
        if ($currencyFormatter !== null) {
            $mask = $this->internationalCurrencySettings($currencyFormatter);
        }
        $this->baseNumberSettings($mask, $currencyCode);
    }

    protected function setCurrencyCode(?string $currencyCode = null): ?string
    {
        if ($currencyCode === null) {
            $currencyCode = CurrencyLookup::lookup($this->locale->getCountryCode());
        }
        $this->currencyCode = $currencyCode;

        return $currencyCode;
    }

    protected function baseNumberSettings(string $mask, ?string $currencyCode = null): void
    {
        if ($this->currencySymbol === null || $this->currencySymbol === false) {
            $this->currencySymbol = CurrencySymbolLookup::lookup($currencyCode) ?? self::CURRENCYCODE_PLACEHOLDER;
        }

        $this->useThousandsSeparator(strpos($mask, ',') !== false);
        $decimalMatch = (bool) (preg_match('/\.[0|#]+/', $mask, $decimals));
        $this->setDecimals($decimalMatch ? strlen($decimals[0]) - 1 : 0);

        $this->mask = $mask;
    }

    protected function internationalCurrencySettings(NumberFormatter $intlFormatter): string
    {
        $this->currencySymbol = $intlFormatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
        $this->intlMask = $intlFormatter->getPattern();
        $this->identifySignPosition();
        $this->identifyCurrencySymbolPosition();

        return $this->intlMask;
    }

    private const IDENTIFY_LEADING_SIGN = '/' .
        '-' . self::NON_BREAKING_SPACE . '?[0|#]' .
        '/u';

    private const IDENTIFY_TRAILING_SIGN = '/' .
        '[0|#]' . self::NON_BREAKING_SPACE . '?-' .
        '/u';

    protected function identifySignPosition(): void
    {
        $hasTrailingSign = false;
        $hasLeadingSign = (bool) (preg_match(self::IDENTIFY_LEADING_SIGN, $this->intlMask, $matches));
        if ($hasLeadingSign === false) {
            $hasTrailingSign = (bool) (preg_match(self::IDENTIFY_TRAILING_SIGN, $this->intlMask, $matches));
        }
        $padded = !empty($matches) && mb_strlen($matches[0]) > 2;

        if ($hasLeadingSign || $hasTrailingSign) {
            $this->trailingSign($hasTrailingSign, $padded ? self::NON_BREAKING_SPACE : '');
        }
    }

    private const IDENTIFY_LEADING_SYMBOL = '/' .
        self::CURRENCYCODE_PLACEHOLDER . self::NON_BREAKING_SPACE . '?' . '[-|0|#]' .
        '/u';

    private const IDENTIFY_TRAILING_SYMBOL = '/' .
        '[-|0|#]' . self::NON_BREAKING_SPACE . '?' . self::CURRENCYCODE_PLACEHOLDER .
        '/u';

    protected function identifyCurrencySymbolPosition(): void
    {
        $hasLeadingSymbol = (bool) (preg_match(self::IDENTIFY_LEADING_SYMBOL, $this->intlMask, $matches));
        if ($hasLeadingSymbol === false) {
            preg_match(self::IDENTIFY_TRAILING_SYMBOL, $this->intlMask, $matches);
        }
        $padded = !empty($matches) && mb_strlen($matches[0]) > 2;

        $this->leading = $hasLeadingSymbol;
        $this->currencySeparator = $padded ? self::NON_BREAKING_SPACE : '';
    }

    public function setCurrencySymbol(string $symbol, ?bool $leading = null, ?string $separator = null): void
    {
        $this->currencySymbol = $symbol;
        $this->leading = $leading ?? $this->leading;
        $separator = ($separator === ' ') ? self::NON_BREAKING_SPACE : $separator;
        $this->currencySeparator = $separator ?? $this->currencySeparator;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    public function getCurrencySymbol(): string
    {
        return $this->currencySymbol;
    }

    protected const SYMBOL_PATTERN_MASK = '/' .
        self::NON_BREAKING_SPACE . '?' . self::CURRENCYCODE_PLACEHOLDER . self::NON_BREAKING_SPACE . '?' .
        '/u';

    protected function setSymbolMask(string $maskSet): string
    {
        $masks = explode(self::MASK_SEPARATOR, $maskSet);

        $paddedCurrencySymbol = ($this->leading === self::CURRENCY_SYMBOL_LEADING)
            ? $this->localeAwareCurrencySymbol() . $this->currencySeparator
            : $this->currencySeparator . $this->localeAwareCurrencySymbol();

        foreach ($masks as &$mask) {
            $mask = preg_replace(self::SYMBOL_PATTERN_MASK, '', $mask);
            $mask = ($this->leading === self::CURRENCY_SYMBOL_LEADING)
                ? $paddedCurrencySymbol . $mask
                : $mask . $paddedCurrencySymbol;
        }

        return implode(self::MASK_SEPARATOR, $masks);
    }

    protected function useIntlFormatMask(): string
    {
        $mask = $this->intlMask;
        $mask = $this->setThousandsMasking($mask);
        $mask = $this->setDecimalsMasking($mask);
        $mask = $this->setSignMasking($mask);
        // Set the currency symbol in the required position, with required padding
        $mask = $this->setSymbolMask($mask);

        return $mask;
    }

    protected function buildMask(): string
    {
        $mask = Number::format();
        $mask = $this->setSymbolMask($mask);

        return $mask;
    }

    public function format(): string
    {
        $mask = ($this->intlMask === null) ? $this->buildMask() : $this->useIntlFormatMask();

        $mask = $this->setColorMasking($mask);

        return $mask;
    }

    protected function localeAwareCurrencySymbol(): string
    {
        $locale = str_replace(Locale::LOCALE_SEPARATOR, '-', $this->locale);

        return "[\${$this->currencySymbol}-{$locale}]";
    }
}
