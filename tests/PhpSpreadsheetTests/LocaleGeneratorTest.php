<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheetInfra\LocaleGenerator;
use PHPUnit\Framework\TestCase;

class LocaleGeneratorTest extends TestCase
{
    public function testLocaleGenerator(): void
    {
        $directory = realpath(__DIR__ . '/../../src/PhpSpreadsheet/Calculation/locale/') ?: '';
        self::assertNotEquals('', $directory);
        $phpSpreadsheetFunctions = Calculation::getFunctions();

        $localeGenerator = new LocaleGenerator(
            $directory . DIRECTORY_SEPARATOR,
            'Translations.xlsx',
            $phpSpreadsheetFunctions
        );
        $localeGenerator->generateLocales();

        $testLocales = [
            'bg',
            'cs',
            'da',
            'de',
            'en',
            'es',
            'fi',
            'fr',
            'hu',
            'it',
            'nb',
            'nl',
            'pl',
            'pt',
            'ru',
            'sv',
            'tr',
        ];

        $count = count(glob($directory . DIRECTORY_SEPARATOR . '*') ?: []) - 1; // exclude Translations.xlsx
        self::assertCount($count, $testLocales);
        $testLocales[] = 'pt_br';
        $testLocales[] = 'en_uk';
        $noconfig = ['en'];
        $nofunctions = ['en', 'en_uk'];
        foreach ($testLocales as $originalLocale) {
            $locale = str_replace('_', DIRECTORY_SEPARATOR, $originalLocale);
            $path = $directory . DIRECTORY_SEPARATOR . $locale;
            if (in_array($originalLocale, $noconfig, true)) {
                self::assertFileDoesNotExist($path . DIRECTORY_SEPARATOR . 'config');
            } else {
                self::assertFileExists($path . DIRECTORY_SEPARATOR . 'config');
            }
            if (in_array($originalLocale, $nofunctions, true)) {
                self::assertFileDoesNotExist($path . DIRECTORY_SEPARATOR . 'functions');
            } else {
                self::assertFileExists($path . DIRECTORY_SEPARATOR . 'functions');
            }
        }
    }
}
