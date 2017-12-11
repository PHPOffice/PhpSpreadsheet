<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class MergedCells extends TestCase
{
    public function providerMergedCells()
    {
        return [
//            ['Html'],
//            ['Xls'],
//            ['Xlsx'],
            ['Ods'],
        ];
    }

    /**
     * @dataProvider providerMergedCells
     *
     * @param string $format
     */
    public function testMergedCells($format)
    {
        $filename = tempnam(sys_get_temp_dir(), strtolower($format));
        $this->writeMergedCells($filename, $format);
        $actual = $this->readMergedCells($filename, $format);
        unlink($filename);

        self::assertSame(1, $actual, "Format $format failed, could not read 1 merged cell");
    }

    private function writeMergedCells($filename, $format)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setCellValue('A1', '1');
        $spreadsheet->getActiveSheet()->setCellValue('B1', '2');
        $spreadsheet->getActiveSheet()->setCellValue('A2', '33');
        $spreadsheet->getActiveSheet()->mergeCells('A2:B2');

        $writer = IOFactory::createWriter($spreadsheet, $format);

        $writer->save($filename);
    }

    private function readMergedCells($filename, $format)
    {
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($filename);
        $n = 0;
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            foreach ($worksheet->getMergeCells() as $cells) {
                ++$n;
            }
        }

        return $n;
    }
}
