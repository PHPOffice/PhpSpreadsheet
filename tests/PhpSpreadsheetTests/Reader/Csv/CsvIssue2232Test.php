<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class CsvIssue2232Test extends TestCase
{
    private IValueBinder $valueBinder;

    private string $locale;

    protected function setUp(): void
    {
        $this->valueBinder = Cell::getValueBinder();
        $this->locale = Settings::getLocale();
    }

    protected function tearDown(): void
    {
        Cell::setValueBinder($this->valueBinder);
        Settings::setLocale($this->locale);
    }

    /**
     * @dataProvider providerIssue2232
     */
    public function testBooleanConversions(bool $useStringBinder, ?bool $preserveBoolString, bool|string $b2Value, bool|string $b3Value): void
    {
        if ($useStringBinder) {
            $binder = new StringValueBinder();
            if (is_bool($preserveBoolString)) {
                $binder->setBooleanConversion($preserveBoolString);
            }
            Cell::setValueBinder($binder);
        }
        $reader = new Csv();
        $filename = 'tests/data/Reader/CSV/issue.2232.csv';
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame($b2Value, $sheet->getCell('B2')->getValue());
        self::assertSame($b3Value, $sheet->getCell('B3')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIssue2232(): array
    {
        return [
            [false, false, false, true],
            [false, null, false, true],
            [false, true, false, true],
            [true, false, false, true],
            [true, null, 'FaLSe', 'tRUE'],
            [true, true, 'FaLSe', 'tRUE'],
        ];
    }

    /**
     * @dataProvider providerIssue2232locale
     */
    public function testBooleanConversionsLocaleAware(bool $useStringBinder, ?bool $preserveBoolString, mixed $b2Value, mixed $b3Value, mixed $b4Value, mixed $b5Value): void
    {
        if ($useStringBinder) {
            $binder = new StringValueBinder();
            if (is_bool($preserveBoolString)) {
                $binder->setBooleanConversion($preserveBoolString);
            }
            Cell::setValueBinder($binder);
        }

        Settings::setLocale('fr');

        $reader = new Csv();
        $filename = 'tests/data/Reader/CSV/issue.2232.csv';
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame($b2Value, $sheet->getCell('B2')->getValue());
        self::assertSame($b3Value, $sheet->getCell('B3')->getValue());
        self::assertSame($b4Value, $sheet->getCell('B4')->getValue());
        self::assertSame($b5Value, $sheet->getCell('B5')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIssue2232locale(): array
    {
        return [
            'string binder preserve boolean string' => [true, true, 'FaLSe', 'tRUE', 'Faux', 'Vrai'],
            'string binder convert boolean string' => [true, false, false, true, false, true],
            'default binder' => [false, null, false, true, false, true],
        ];
    }
}
