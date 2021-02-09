<?php

namespace PhpOffice\PhpSpreadsheet\Helper\NumberFormat;

use NumberFormatter;

class Number
{
    protected const FORMAT_STYLE = Number::FORMAT_STYLE_DECIMAL;

    protected const FORMAT_STYLE_DECIMAL = 1;
    protected const FORMAT_STYLE_CURRENCY = 2;
    protected const FORMAT_STYLE_ACCOUNTING = 4;

    protected const MASK_SEPARATOR = ';';
    protected const THOUSANDS_SEPARATOR = ',';
    protected const DECIMAL_SEPARATOR = '.';

    public const NON_BREAKING_SPACE = ' ';

    protected const SIGN_POSITIVE = '+';
    protected const SIGN_NEGATIVE = '-';

    protected const MASK_POSITIVE_VALUE = 0;
    protected const MASK_NEGATIVE_VALUE = 1;
    protected const MASK_ZERO_VALUE = 2;

    /**
     * Note that not all language communities in these countries use lakh,
     *      and there are other countries like Myanmar and Nepal where some minority language communities do use lakh
     *      but to distinguish at that level, we need full Intl support enabled.
     */
    protected const LAKH_COUNTRIES = [
        'BD',   // Bangladesh
        'BT',   // Bhutan
        'IN',   // India
        'LK',   // Sri Lanka
        'PK',   // Pakistan
    ];

    protected $decimals = 2;
    protected $thousands = true;

    protected $trailingSign = false;
    protected $displayPositiveSign = false;
    protected $signSeparator = '';

    protected $locale;
    protected $intlMask;
    protected $mask;

    public function __construct(string $locale = 'en_US', int $decimals = 2, bool $thousandsSeparator = true)
    {
        $countryCode = $this->setLocale($locale)->getCountryCode();

        $mask = in_array($countryCode, self::LAKH_COUNTRIES, true) ? '#,##,##0.###' : '#,##0.###';

        $formatterLocale = "{$this->locale}.UTF8";
        $numberFormatter = $this->internationalFormatter($formatterLocale);
        if ($numberFormatter !== null) {
            $this->intlMask = $numberFormatter->getPattern();
            $mask = $this->intlMask;
        }
        $this->mask = $mask;

        $this->setDecimals($decimals);
        $this->useThousandsSeparator($thousandsSeparator);
    }

    protected function setLocale(string $locale): Locale
    {
        $this->locale = new Locale($locale);

        return $this->locale;
    }

    private function internationalFormatterStyle(): int
    {
        switch (static::FORMAT_STYLE) {
            case self::FORMAT_STYLE_ACCOUNTING:
                return NumberFormatter::CURRENCY_ACCOUNTING;
            case self::FORMAT_STYLE_CURRENCY:
                return NumberFormatter::CURRENCY;
        }

        return NumberFormatter::DECIMAL;
    }

    protected function internationalFormatter(string $locale): ?NumberFormatter
    {
        if (extension_loaded('intl')) {
            $intlStyle = $this->internationalFormatterStyle();
            $intlFormatter = new NumberFormatter($locale, $intlStyle);
            if ($intlFormatter !== false) {
                return $intlFormatter;
            }
        }

        return null;
    }

    public function usingIntl(): bool
    {
        return $this->intlMask !== null;
    }

    public function setDecimals(int $decimals = 0): void
    {
        $this->decimals = $decimals;
    }

    public function useThousandsSeparator(bool $separator = true): void
    {
        $this->thousands = $separator;
    }

    public function trailingSign(bool $trailingSign = false, $signSeparator = ''): void
    {
        $this->trailingSign = $trailingSign;
        $this->signSeparator = $signSeparator;
    }

    public function displayPositiveSign(bool $displayPositiveSign = false): void
    {
        $this->displayPositiveSign = $displayPositiveSign;
    }

    protected const DECIMAL_PATTERN_MASK = '/' .
    '(?:0\\' . self::DECIMAL_SEPARATOR . ')(0*)' .
    '/u';

    protected function setDecimalsMasking(string $mask): string
    {
        $mask = rtrim($mask, '#');

        // Set requested number of decimal places
        $decimalReplacement = ($this->decimals === 0)
            ? '0'
            : '0' . self::DECIMAL_SEPARATOR . str_repeat('0', $this->decimals);

        return preg_replace(self::DECIMAL_PATTERN_MASK, $decimalReplacement, $mask);
    }

    protected function setThousandsMasking(string $mask): string
    {
        if ($this->thousands === false) {
            $mask = str_replace(self::THOUSANDS_SEPARATOR, '', $mask);
            $mask = preg_replace('/#+0/', '0', $mask);
        }

        return $mask;
    }

    protected const SIGN_PATTERN_MASK = '/' .
        self::NON_BREAKING_SPACE . '?[-+]' . self::NON_BREAKING_SPACE . '?' .
        '/u';

    protected const SIGN_TRAILING_MASK = '/([0#])(?!.*[0#])/u';

    protected const SIGN_LEADING_MASK = '/([0#])/u';

    protected function setSignMasking(string $maskSet): string
    {
        $masks = explode(self::MASK_SEPARATOR, $maskSet);

        $mask = $masks[self::MASK_POSITIVE_VALUE];
        $mask = preg_replace(self::SIGN_PATTERN_MASK, '', $mask);
        $masks[self::MASK_POSITIVE_VALUE] = $mask;

        if ($this->displayPositiveSign === true) {
            $masks[self::MASK_ZERO_VALUE] = $mask;
            $masks[self::MASK_POSITIVE_VALUE] = $this->trailingSign
                ? preg_replace(self::SIGN_TRAILING_MASK, '$1' . $this->signSeparator . self::SIGN_POSITIVE, $mask)
                : preg_replace(self::SIGN_LEADING_MASK, self::SIGN_POSITIVE . $this->signSeparator . '$1', $mask, 1);
        }

        if ($this->trailingSign === true || $this->displayPositiveSign === true || $this->signSeparator !== '') {
            $masks[self::MASK_NEGATIVE_VALUE] = $this->trailingSign
                ? preg_replace(self::SIGN_TRAILING_MASK, '$1' . $this->signSeparator . self::SIGN_NEGATIVE, $mask)
                : preg_replace(self::SIGN_LEADING_MASK, self::SIGN_NEGATIVE . $this->signSeparator . '$1', $mask, 1);
        }

        ksort($masks);

        return implode(self::MASK_SEPARATOR, $masks);
    }

    public function format(): string
    {
        $mask = $this->mask;
        $mask = $this->setDecimalsMasking($mask);
        $mask = $this->setThousandsMasking($mask);
        $mask = $this->setSignMasking($mask);

        return str_replace(self::NON_BREAKING_SPACE, '_', $mask);
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
