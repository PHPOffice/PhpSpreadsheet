<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard;

use NumberFormatter;
use PhpOffice\PhpSpreadsheet\Exception;

class Accounting extends CurrencyBase
{
    protected ?bool $overrideSpacing = true;

    protected ?CurrencyNegative $overrideNegative = CurrencyNegative::parentheses;

    /**
     * @throws Exception if the Intl extension and ICU version don't support Accounting formats
     */
    protected function getLocaleFormat(): string
    {
        if (self::icuVersion() < 53.0) {
            // @codeCoverageIgnoreStart
            throw new Exception('The Intl extension does not support Accounting Formats without ICU 53');
            // @codeCoverageIgnoreEnd
        }

        // Scrutinizer does not recognize CURRENCY_ACCOUNTING
        $formatter = new Locale($this->fullLocale, NumberFormatter::CURRENCY_ACCOUNTING);
        $mask = $formatter->format($this->stripLeadingRLM);
        if ($this->decimals === 0) {
            $mask = (string) preg_replace('/\.0+/miu', '', $mask);
        }

        return str_replace('¤', $this->formatCurrencyCode(), $mask);
    }

    public static function icuVersion(): float
    {
        [$major, $minor] = explode('.', INTL_ICU_VERSION);

        return (float) "{$major}.{$minor}";
    }

    private function formatCurrencyCode(): string
    {
        if ($this->locale === null) {
            return $this->currencyCode . '*';
        }

        return "[\${$this->currencyCode}-{$this->locale}]";
    }
}
