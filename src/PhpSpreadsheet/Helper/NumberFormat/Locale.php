<?php

namespace PhpOffice\PhpSpreadsheet\Helper\NumberFormat;

class Locale
{
    public const LOCALE_SEPARATOR = '_';

    protected $locale;

    protected $language;

    protected $countryCode = 'US';

    public function __construct(string $locale = 'en_US')
    {
        $locale = strtolower(strtok($locale, '@. '));

        $language = $locale;
        if (strpos($locale, self::LOCALE_SEPARATOR) !== false) {
            [$language, $countryCode] = explode(self::LOCALE_SEPARATOR, $locale);
        }

        $this->language = $language;

        $this->locale = $language;
        if (!empty($countryCode)) {
            $this->countryCode = strtoupper($countryCode);
            $this->locale .= self::LOCALE_SEPARATOR . $this->countryCode;
        }
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function __toString(): string
    {
        return $this->locale;
    }
}
