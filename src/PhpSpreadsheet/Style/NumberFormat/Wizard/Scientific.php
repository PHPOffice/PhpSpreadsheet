<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard;

use PhpOffice\PhpSpreadsheet\Exception;

class Scientific extends NumberBase implements Wizard
{
    /**
     * @param int $decimals number of decimal places to display, in the range 0-30
     * @param ?string $locale Set the locale for the scientific format; or leave as the default null.
     *          Locale has no effect for Scientific Format values, and is retained here for compatibility
     *              with the other Wizards.
     *          If provided, Locale values must be a valid formatted locale string (e.g. 'en-GB', 'fr', uz-Arab-AF).
     *
     * @throws Exception If a provided locale code is not a valid format
     */
    public function __construct(int $decimals = 2, ?string $locale = null)
    {
        $this->setDecimals($decimals);
        $this->setLocale($locale);
    }

    protected function getLocaleFormat(): string
    {
        return $this->format();
    }

    public function format(): string
    {
        return sprintf('0%sE+00', $this->decimals > 0 ? '.' . str_repeat('0', $this->decimals) : null);
    }
}
