<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DefinedNameBoolTest extends TestCase
{
    private string $locale;

    protected function setUp(): void
    {
        $calculation = Calculation::getInstance();
        $this->locale = $calculation->getLocale();
    }

    protected function tearDown(): void
    {
        $calculation = Calculation::getInstance();
        $calculation->setLocale($this->locale);
    }

    #[DataProvider('providerFormulaAsBoolString')]
    public function testEnglish(mixed $expected, string $value): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->addDefinedName(
            DefinedName::createInstance('defname', $sheet, $value)
        );
        $sheet->getCell('A1')->setValue('=defname');
        self::assertSame($expected, $sheet->getCell('A1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    /** @return array<string, array{mixed, string}> */
    public static function providerFormulaAsBoolString(): array
    {
        return [
            'uppercase TRUE' => [true, 'TRUE'],
            'lowercase true' => [true, 'true'],
            'mixed TrUE' => [true, 'TrUE'],
            'uppercase FALSE' => [false, 'FALSE'],
            'lowercase false' => [false, 'false'],
            'mixed fAlse' => [false, 'fAlse'],
            'istina uppercase' => ['#NAME?', 'ИСТИНА'],
            'istina lowercase' => ['#NAME?', 'ИСТИНА'],
            'istina mixed' => ['#NAME?', 'ИСТИНА'],
            'lozh uppercase' => ['#NAME?', 'ЛОЖЬ'],
            'lozh lowercase' => ['#NAME?', 'лОжь'],
            'lozh mixed' => ['#NAME?', 'ЛОЖЬ'],
        ];
    }

    #[DataProvider('providerFormulaAsBoolStringRussian')]
    public function testRussian(mixed $expected, string $value): void
    {
        $calculation = Calculation::getInstance();
        $calculation->setLocale('ru');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->addDefinedName(
            DefinedName::createInstance('defname', $sheet, $value)
        );
        $sheet->getCell('A1')->setValue('=defname');
        self::assertSame($expected, $sheet->getCell('A1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    /** @return array<string, array{mixed, string}> */
    public static function providerFormulaAsBoolStringRussian(): array
    {
        return [
            'uppercase TRUE' => [true, 'TRUE'],
            'lowercase true' => [true, 'true'],
            'mixed TrUE' => [true, 'TrUE'],
            'uppercase FALSE' => [false, 'FALSE'],
            'lowercase false' => [false, 'false'],
            'mixed fAlse' => [false, 'fAlse'],
            'istina uppercase' => [true, 'ИСТИНА'],
            'istina lowercase' => [true, 'истина'],
            'istina mixed' => [true, 'Истина'],
            'lozh uppercase' => [false, 'ЛОЖЬ'],
            'lozh lowercase' => [false, 'ложь'],
            'lozh mixed' => [false, 'лОжь'],
        ];
    }
}
