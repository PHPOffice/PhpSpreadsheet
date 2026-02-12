<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

/**
 * @TODO The class doesn't read the bold/italic/underline properties (rich text)
 */
class CurrencyTest extends TestCase
{
    private const ODS_CURRENCY_FILE = 'tests/data/Reader/Ods/currency4.ods';

    public function testCurrencies(): void
    {
        $reader = new Ods();
        $spreadsheet = $reader->load(self::ODS_CURRENCY_FILE);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('$1.23 ', $sheet->getCell('A1')->getFormattedValue(), 'stored as Canada dollars');
        self::assertSame('£2.23 ', $sheet->getCell('A2')->getFormattedValue(), 'stored as Great Britain pounds');
        self::assertSame('￥3 ', $sheet->getCell('A3')->getFormattedValue(), 'stored as Japanese yen with fraction which is descarded');
        self::assertSame('￥4.23 ', $sheet->getCell('A4')->getFormattedValue(), 'stored as Chinese yuan');
        self::assertSame('$6.23 ', $sheet->getCell('A5')->getFormattedValue(), 'stored as US dollars');
        self::assertSame('7.23 €', $sheet->getCell('A6')->getFormattedValue(), 'stored as euros with a locale of France');
        $spreadsheet->disconnectWorksheets();
    }
}
