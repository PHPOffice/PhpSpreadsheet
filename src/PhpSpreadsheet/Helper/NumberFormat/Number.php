<?php

namespace PhpOffice\PhpSpreadsheet\Helper\NumberFormat;

use NumberFormatter;
use PhpOffice\PhpSpreadsheet\Style\Color;

class Number extends BaseNumberFormatter
{
    protected const FORMAT_STYLE = self::FORMAT_STYLE_DECIMAL;

    protected const FORMAT_STYLE_DECIMAL = 1;
    protected const FORMAT_STYLE_CURRENCY = 2;
    protected const FORMAT_STYLE_ACCOUNTING = 4;

    /**
     * Note that not all language communities in these countries use lakh,
     *      and there are other countries like Myanmar and Nepal where some minority language communities do use lakh
     *      but to distinguish at that level, we need full Intl support enabled.
     */
    protected const LAKH_COUNTRIES = [
        'BD', // Bangladesh
        'BT', // Bhutan
        'IN', // India
        'LK', // Sri Lanka
        'PK', // Pakistan
    ];

    protected $decimals = 2;

    protected $thousands = true;

    protected $trailingSign = false;

    protected $displayPositiveSign = false;

    protected $signSeparator = '';

    protected $locale;

    protected $intlMask;

    protected $mask;

    protected $colors = [];

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

    public static function icuVersion(): ?float
    {
        if (!extension_loaded('intl')) {
            return null;
        }

        [$major, $minor] = explode('.', INTL_ICU_VERSION);
        $icuVersion = (float) "{$major}.{$minor}";

        return $icuVersion;
    }

    private function internationalFormatterStyle(): int
    {
        switch (static::FORMAT_STYLE) {
            case self::FORMAT_STYLE_ACCOUNTING:
                return (version_compare(PHP_VERSION, '7.4.1', '>=') || self::icuVersion() >= 53.0)
                    // CURRENCY_ACCOUNTING requires PHP 7.4.1 and ICU 53; default to CURRENCY if it isn't available.
                    ? NumberFormatter::CURRENCY_ACCOUNTING
                    : NumberFormatter::CURRENCY;
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

            return $intlFormatter;
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

    protected function validateColor($color = null): ?string
    {
        if ($color === null) {
            return $color;
        }

        if (is_numeric($color) && $color >= 1 && $color <= 56) {
            $color = (int) $color;

            return "Color{$color}";
        }

        $color = ucfirst(strtolower($color));
        if (!in_array($color, Color::NAMED_COLORS, true)) {
            return null;
        }

        return $color;
    }

    public function setColors($positiveColor, $negativeColor = null, $zeroColor = null): void
    {
        $this->colors[self::MASK_POSITIVE_VALUE] = $this->validateColor($positiveColor);
        $this->colors[self::MASK_NEGATIVE_VALUE] = $this->validateColor($negativeColor);
        $this->colors[self::MASK_ZERO_VALUE] = $this->validateColor($zeroColor);
    }

    protected function zeroValueMask(): string
    {
        return ($this->decimals === 0)
            ? '0'
            : '0' . self::DECIMAL_SEPARATOR . str_repeat(self::DIGIT_ALWAYS_DISPLAY, $this->decimals);
    }

    public function format(): string
    {
        $mask = $this->mask;
        $mask = $this->setDecimalsMasking($mask);
        $mask = $this->setThousandsMasking($mask);
        $mask = $this->setSignMasking($mask);

        $mask = $this->setColorMasking($mask);

        return $mask;
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
