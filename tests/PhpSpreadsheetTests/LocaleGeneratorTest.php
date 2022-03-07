<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheetInfra\LocaleGenerator;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class LocaleGeneratorTest extends TestCase
{
    public function testLocaleGenerator(): void
    {
        $phpSpreadsheetFunctionsProperty = (new ReflectionClass(Calculation::class))
            ->getProperty('phpSpreadsheetFunctions');
        $phpSpreadsheetFunctionsProperty->setAccessible(true);
        $phpSpreadsheetFunctions = $phpSpreadsheetFunctionsProperty->getValue();

        $localeGenerator = new LocaleGenerator(
            (string) realpath(__DIR__ . '/../../src/PhpSpreadsheet/Calculation/locale/'),
            'Translations.xlsx',
            $phpSpreadsheetFunctions
        );
        $localeGenerator->generateLocales();

        $testLocales = [
            'fr',
            'nl',
            'pt',
            'pt_br',
            'ru',
        ];

        foreach ($testLocales as $locale) {
            $locale = str_replace('_', '/', $locale);
            $path = realpath(__DIR__ . "/../../src/PhpSpreadsheet/Calculation/locale/{$locale}");
            self::assertFileExists("{$path}/config");
            self::assertFileExists("{$path}/functions");
        }
    }
}
