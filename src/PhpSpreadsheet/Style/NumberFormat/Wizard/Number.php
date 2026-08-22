<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard;

use PhpOffice\PhpSpreadsheet\Exception;

class Number extends NumberBase implements Wizard
{
    public const WITH_THOUSANDS_SEPARATOR = true;

    public const WITHOUT_THOUSANDS_SEPARATOR = false;

    protected bool $thousandsSeparator = true;

    /**
     * @param int $decimals number of decimal places to display, in the range 0-30
     * @param bool $thousandsSeparator indicator whether the thousands separator should be used, or not
     * @param ?string $locale Set the locale for the number format; or leave as the default null.
     *          Locale has no effect for Number Format values, and is retained here only for compatibility
     *              with the other Wizards.
     *          If provided, Locale values must be a valid formatted locale string (e.g. 'en-GB', 'fr', uz-Arab-AF).
     *
     * @throws Exception If a provided locale code is not a valid format
     */
    public function __construct(
        int $decimals = 2,
        bool $thousandsSeparator = self::WITH_THOUSANDS_SEPARATOR,
        ?string $locale = null
    ) {
        $this->setDecimals($decimals);
        $this->setThousandsSeparator($thousandsSeparator);
        $this->setLocale($locale);
    }

    public function setThousandsSeparator(bool $thousandsSeparator = self::WITH_THOUSANDS_SEPARATOR): void
    {
        $this->thousandsSeparator = $thousandsSeparator;
    }

    /**
     * As MS Excel cannot easily handle Lakh, which is the only locale-specific Number format variant,
     *       we don't use locale with Numbers.
     */
    protected function getLocaleFormat(): string
    {
        return $this->format();
    }

    public function format(): string
    {
        return sprintf(
            '%s0%s',
            $this->thousandsSeparator ? '#,##' : null,
            $this->decimals > 0 ? '.' . str_repeat('0', $this->decimals) : null
        );
    }
}
