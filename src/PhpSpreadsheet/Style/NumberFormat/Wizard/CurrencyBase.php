<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard;

use NumberFormatter;
use PhpOffice\PhpSpreadsheet\Exception;

class CurrencyBase extends Number
{
    public const LEADING_SYMBOL = true;

    public const TRAILING_SYMBOL = false;

    public const SYMBOL_WITH_SPACING = true;

    public const SYMBOL_WITHOUT_SPACING = false;

    protected string $currencyCode = '$';

    protected bool $currencySymbolPosition = self::LEADING_SYMBOL;

    protected bool $currencySymbolSpacing = self::SYMBOL_WITHOUT_SPACING;

    protected const DEFAULT_STRIP_LEADING_RLM = false;

    protected bool $stripLeadingRLM = self::DEFAULT_STRIP_LEADING_RLM;

    public const DEFAULT_NEGATIVE = CurrencyNegative::minus;

    protected CurrencyNegative $negative = CurrencyNegative::minus;

    protected ?bool $overrideSpacing = null;

    protected ?CurrencyNegative $overrideNegative = null;

    // Not sure why original code uses nbsp
    private string $spaceOrNbsp = ' '; // or "\u{a0}"

    /**
     * @param string $currencyCode the currency symbol or code to display for this mask
     * @param int $decimals number of decimal places to display, in the range 0-30
     * @param bool $thousandsSeparator indicator whether the thousands separator should be used, or not
     * @param bool $currencySymbolPosition indicates whether the currency symbol comes before or after the value
     *              Possible values are Currency::LEADING_SYMBOL and Currency::TRAILING_SYMBOL
     * @param bool $currencySymbolSpacing indicates whether there is spacing between the currency symbol and the value
     *              Possible values are Currency::SYMBOL_WITH_SPACING and Currency::SYMBOL_WITHOUT_SPACING
     *              However, Currency always uses WITHOUT and Accounting always uses WITH
     * @param ?string $locale Set the locale for the currency format; or leave as the default null.
     *          If provided, Locale values must be a valid formatted locale string (e.g. 'en-GB', 'fr', uz-Arab-AF).
     *          Note that setting a locale will override any other settings defined in this class
     *          other than the currency code; or decimals (unless the decimals value is set to 0).
     * @param bool $stripLeadingRLM remove leading RLM added with
     *          ICU 72.1+.
     * @param CurrencyNegative $negative How to display negative numbers.
     *                         Always use parentheses for Accounting.
     *                         4 options for Currency.
     *
     * @throws Exception If a provided locale code is not a valid format
     */
    public function __construct(
        string $currencyCode = '$',
        int $decimals = 2,
        bool $thousandsSeparator = true,
        bool $currencySymbolPosition = self::LEADING_SYMBOL,
        bool $currencySymbolSpacing = self::SYMBOL_WITHOUT_SPACING,
        ?string $locale = null,
        bool $stripLeadingRLM = self::DEFAULT_STRIP_LEADING_RLM,
        CurrencyNegative $negative = CurrencyNegative::minus
    ) {
        $this->setCurrencyCode($currencyCode);
        $this->setThousandsSeparator($thousandsSeparator);
        $this->setDecimals($decimals);
        $this->setCurrencySymbolPosition($currencySymbolPosition);
        $this->setCurrencySymbolSpacing($currencySymbolSpacing);
        $this->setLocale($locale);
        $this->stripLeadingRLM = $stripLeadingRLM;
        $this->negative = $negative;
    }

    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    public function setCurrencySymbolPosition(bool $currencySymbolPosition = self::LEADING_SYMBOL): void
    {
        $this->currencySymbolPosition = $currencySymbolPosition;
    }

    public function setCurrencySymbolSpacing(bool $currencySymbolSpacing = self::SYMBOL_WITHOUT_SPACING): void
    {
        $this->currencySymbolSpacing = $currencySymbolSpacing;
    }

    public function setStripLeadingRLM(bool $stripLeadingRLM): void
    {
        $this->stripLeadingRLM = $stripLeadingRLM;
    }

    public function setNegative(CurrencyNegative $negative): void
    {
        $this->negative = $negative;
    }

    protected function getLocaleFormat(): string
    {
        $formatter = new Locale($this->fullLocale, NumberFormatter::CURRENCY);
        $mask = $formatter->format($this->stripLeadingRLM);
        if ($this->decimals === 0) {
            $mask = (string) preg_replace('/\.0+/miu', '', $mask);
        }

        return str_replace('¤', $this->formatCurrencyCode(), $mask);
    }

    private function formatCurrencyCode(): string
    {
        if ($this->locale === null) {
            return $this->currencyCode;
        }

        return "[\${$this->currencyCode}-{$this->locale}]";
    }

    public function format(): string
    {
        if ($this->localeFormat !== null) {
            return $this->localeFormat;
        }
        $symbolWithSpacing = $this->overrideSpacing ?? ($this->currencySymbolSpacing === self::SYMBOL_WITH_SPACING);
        $negative = $this->overrideNegative ?? $this->negative;

        // format if positive
        $format = '_(';
        if ($this->currencySymbolPosition === self::LEADING_SYMBOL) {
            $format .= '"' . $this->currencyCode . '"';
            if (preg_match('/^[A-Z]{3}$/i', $this->currencyCode) === 1) {
                $format .= $this->spaceOrNbsp;
            }
            if (preg_match('/^[A-Z]{3}$/i', $this->currencyCode) === 1) {
                $format .= $this->spaceOrNbsp;
            }
            if ($symbolWithSpacing) {
                $format .= '*' . $this->spaceOrNbsp;
            }
        }
        $format .= $this->thousandsSeparator ? '#,##0' : '0';
        if ($this->decimals > 0) {
            $format .= '.' . str_repeat('0', $this->decimals);
        }
        if ($this->currencySymbolPosition === self::TRAILING_SYMBOL) {
            if ($symbolWithSpacing) {
                $format .= $this->spaceOrNbsp;
            } elseif (preg_match('/^[A-Z]{3}$/i', $this->currencyCode) === 1) {
                $format .= $this->spaceOrNbsp;
            }
            $format .= '[$' . $this->currencyCode . ']';
        }
        $format .= '_)';

        // format if negative
        $format .= ';_(';
        $format .= $negative->color();
        $negativeStart = $negative->start();
        if ($this->currencySymbolPosition === self::LEADING_SYMBOL) {
            if ($negativeStart === '-' && !$symbolWithSpacing) {
                $format .= $negativeStart;
            }
            $format .= '"' . $this->currencyCode . '"';
            if (preg_match('/^[A-Z]{3}$/i', $this->currencyCode) === 1) {
                $format .= $this->spaceOrNbsp;
            }
            if ($symbolWithSpacing) {
                $format .= '*' . $this->spaceOrNbsp;
            }
            if ($negativeStart === '\(' || ($symbolWithSpacing && $negativeStart === '-')) {
                $format .= $negativeStart;
            }
        } else {
            $format .= $negative->start();
        }
        $format .= $this->thousandsSeparator ? '#,##0' : '0';
        if ($this->decimals > 0) {
            $format .= '.' . str_repeat('0', $this->decimals);
        }
        $format .= $negative->end();
        if ($this->currencySymbolPosition === self::TRAILING_SYMBOL) {
            if ($symbolWithSpacing) {
                // Do nothing - I can't figure out how to get
                // everything to align if I put any kind of space here.
                //$format .= "\u{2009}";
            } elseif (preg_match('/^[A-Z]{3}$/i', $this->currencyCode) === 1) {
                $format .= $this->spaceOrNbsp;
            }
            $format .= '[$' . $this->currencyCode . ']';
        }
        if ($this->currencySymbolPosition === self::TRAILING_SYMBOL) {
            $format .= '_)';
        } elseif ($symbolWithSpacing && $negativeStart === '-') {
            $format .= ' ';
        }
        // format if zero
        $format .= ';_(';
        if ($this->currencySymbolPosition === self::LEADING_SYMBOL) {
            $format .= '"' . $this->currencyCode . '"';
        }
        if ($symbolWithSpacing) {
            if ($this->currencySymbolPosition === self::LEADING_SYMBOL) {
                $format .= '*' . $this->spaceOrNbsp;
            }
            $format .= '"-"';
            if ($this->decimals > 0) {
                $format .= str_repeat('?', $this->decimals);
            }
        } else {
            if (preg_match('/^[A-Z]{3}$/i', $this->currencyCode) === 1) {
                $format .= $this->spaceOrNbsp;
            }
            $format .= '0';
            if ($this->decimals > 0) {
                $format .= '.' . str_repeat('0', $this->decimals);
            }
        }
        if ($this->currencySymbolPosition === self::TRAILING_SYMBOL) {
            if ($symbolWithSpacing) {
                $format .= $this->spaceOrNbsp;
            }
            $format .= '[$' . $this->currencyCode . ']';
        }
        $format .= '_)';
        // format if text
        $format .= ';_(@_)';

        return $format;
    }
}
