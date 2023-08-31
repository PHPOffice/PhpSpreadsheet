<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class CsvIssue2232Test extends TestCase
{
    /**
     * @var IValueBinder
     */
    private $valueBinder;

    /** @var string */
    private $locale;

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
     *
     * @param mixed $b2Value
     * @param mixed $b3Value
     */
    public function testBooleanConversions(bool $useStringBinder, ?bool $preserveBoolString, $b2Value, $b3Value): void
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
     *
     * @param mixed $b4Value
     * @param mixed $b5Value
     */
    public function testBooleanConversionsLocaleAware(bool $useStringBinder, ?bool $preserveBoolString, $b4Value, $b5Value): void
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
        self::assertSame($b4Value, $sheet->getCell('B4')->getValue());
        self::assertSame($b5Value, $sheet->getCell('B5')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIssue2232locale(): array
    {
        return [
            [true, true, 'Faux', 'Vrai'],
            [true, true, 'Faux', 'Vrai'],
            [false, false, false, true],
            [false, false, false, true],
        ];
    }
}
