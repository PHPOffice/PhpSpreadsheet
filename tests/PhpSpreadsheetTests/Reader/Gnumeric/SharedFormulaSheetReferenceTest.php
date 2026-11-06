<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PHPUnit\Framework\TestCase;

class SharedFormulaSheetReferenceTest extends TestCase
{
    private string $outfile = '';

    protected function tearDown(): void
    {
        if ($this->outfile !== '') {
            unlink($this->outfile);
            $this->outfile = '';
        }
    }

    public function testSheetQualifiedReferencesAreShifted(): void
    {
        $this->outfile = File::temporaryFilename();
        file_put_contents($this->outfile, <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <gnm:Workbook xmlns:gnm="http://www.gnumeric.org/v10.dtd">
              <gnm:Sheets>
                <gnm:Sheet>
                  <gnm:Name>S</gnm:Name>
                  <gnm:Cells>
                    <gnm:Cell Row="0" Col="0" ExprID="1">=Data!A1*1</gnm:Cell>
                    <gnm:Cell Row="0" Col="1" ExprID="2">=S!D1+1</gnm:Cell>
                    <gnm:Cell Row="0" Col="2" ExprID="3">=Data!$A$1&amp;"Data!A1"</gnm:Cell>
                    <gnm:Cell Row="1" Col="0" ExprID="1"/>
                    <gnm:Cell Row="1" Col="1" ExprID="2"/>
                    <gnm:Cell Row="1" Col="2" ExprID="3"/>
                  </gnm:Cells>
                </gnm:Sheet>
                <gnm:Sheet>
                  <gnm:Name>Data</gnm:Name>
                  <gnm:Cells>
                    <gnm:Cell Row="0" Col="0" ValueType="40">10</gnm:Cell>
                    <gnm:Cell Row="1" Col="0" ValueType="40">20</gnm:Cell>
                  </gnm:Cells>
                </gnm:Sheet>
              </gnm:Sheets>
            </gnm:Workbook>
            XML);

        $spreadsheet = (new Gnumeric())->load($this->outfile);
        $sheet = $spreadsheet->getSheetByNameOrThrow('S');
        self::assertSame('=Data!A1*1', $sheet->getCell('A1')->getValue());
        self::assertSame('=Data!A2*1', $sheet->getCell('A2')->getValue());
        self::assertSame(20, $sheet->getCell('A2')->getCalculatedValue());
        self::assertSame('=S!D2+1', $sheet->getCell('B2')->getValue());
        self::assertSame('=Data!$A$1&"Data!A1"', $sheet->getCell('C2')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
