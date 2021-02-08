<?php

namespace PhpOffice\PhpSpreadsheet\Helper\NumberFormat;

use NumberFormatter;
use function Symfony\Component\String\s;

class Number
{
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
     * Note that not all language groups in these countries use lakh,
     *      and there are other contries like Myanmar and Nepal where some minority language groups do use lakh
     *      but to distinguish at that level, we need full Intl support enabled
     */
    protected const LAKH_COUNTRIES = [
        'BD',   // Bangladesh
        'BT',   // Bhutan
        'IN',   // India
        'LK',   // Sri Lanka
        'PK',   // Pakistan
    ];

    protected $decimals = 0;
    protected $thousands = true;
    protected $trailingSign = false;
    protected $displayPositiveSign = false;
    protected $signSeparator = '';

    protected $locale;
    protected $intlMask;
    protected $mask;

    public function __construct(string $locale = 'en_US', int $decimals = 2, bool $thousandsSeparator = true)
    {
        $locale = new Locale($locale);
        $countryCode = $locale->getCountryCode();
        $this->locale = $locale->getLocale();

        $mask = in_array($countryCode, self::LAKH_COUNTRIES, true) ? '#,##,##0.###' : '#,##0.###';
        $locale = "{$locale}.UTF8";
        $numberFormatter = $this->internationalFormatter($locale, self::FORMAT_STYLE_DECIMAL);
        if ($numberFormatter !== null) {
            $this->intlMask = $numberFormatter->getPattern();
            $mask = $this->intlMask;
        }
        $this->mask = $mask;

        $this->setDecimals($decimals);
        $this->useThousandsSeparator($thousandsSeparator);
    }

    private function internationalFormatterStyle(int $style)
    {
        switch ($style) {
            case self::FORMAT_STYLE_ACCOUNTING:
                return NumberFormatter::CURRENCY_ACCOUNTING;
            case self::FORMAT_STYLE_CURRENCY:
                return NumberFormatter::CURRENCY;
        }

        return NumberFormatter::DECIMAL;
    }

    protected function internationalFormatter(string $locale, int $style): ?NumberFormatter
    {
        if (extension_loaded('intl')) {
            $intlStyle = $this->internationalFormatterStyle($style);
            $intlFormatter = new NumberFormatter($locale, $intlStyle);
            if ($intlFormatter !== false) {
                return $intlFormatter;
            }
        }

        return null;
    }

    public function hasIntlMask(): bool
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

    public function format(): string
    {
        $mask = rtrim($this->mask, '.#');
        if ($this->thousands === false) {
            $mask = str_replace(self::THOUSANDS_SEPARATOR, '', $mask);
            $mask = preg_replace('/#+0/', '0', $mask);
        }
        if ($this->decimals > 0) {
            $mask .= self::DECIMAL_SEPARATOR . str_repeat('0', $this->decimals);
        }

        $masks[self::MASK_POSITIVE_VALUE] = $mask;
        if ($this->displayPositiveSign === true) {
            $masks[self::MASK_ZERO_VALUE] = $mask;
            $masks[self::MASK_POSITIVE_VALUE] = $this->trailingSign
                ? $mask . $this->signSeparator . self::SIGN_POSITIVE
                : self::SIGN_POSITIVE . $this->signSeparator . $mask;
        }

        if ($this->trailingSign === true || $this->displayPositiveSign === true || $this->signSeparator !== '') {
            $masks[self::MASK_NEGATIVE_VALUE] = $this->trailingSign
                ? $mask . $this->signSeparator . self::SIGN_NEGATIVE
                : self::SIGN_NEGATIVE . $this->signSeparator . $mask;
        }
        ksort($masks);

        return implode(self::MASK_SEPARATOR, $masks);
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
