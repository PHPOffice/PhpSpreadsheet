<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Reader\BaseReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;
use PHPUnit\Framework\Attributes\DataProvider;

class Issue347Test extends AbstractFunctional
{
    public function readerPreserveCr(BaseReader $reader): void
    {
        $binder = new DefaultValueBinder();
        $binder->setPreserveCr(true);
        $reader->setValueBinder($binder);
    }

    #[DataProvider('providerType')]
    public function testPreserveCr(string $format): void
    {
        $s1 = "AB\r\nC\tD";
        $spreadsheet = new Spreadsheet();
        $binder = new DefaultValueBinder();
        $binder->setPreserveCr(true);
        $spreadsheet->setValueBinder($binder);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($s1);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format, readerCustomizer: $this->readerPreserveCr(...));
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $s2 = $rsheet->getCell('A1')->getValue();
        self::assertSame($s1, $s2);
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    #[DataProvider('providerType')]
    public function testNoPreserveCr(string $format): void
    {
        $s1 = "AB\r\nC\tD";
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($s1);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $s2 = $rsheet->getCell('A1')->getValue();
        self::assertNotEquals($s1, $s2);
        self::assertSame("AB\nC\tD", $s2);
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public static function providerType(): array
    {
        return [['Xls'], ['Xlsx'], ['Ods']];
    }
}
