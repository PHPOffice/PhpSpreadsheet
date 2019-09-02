<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class CalculateFromFileTest extends TestCase
{
    public function branchPruningModeDataProvider(): array
    {
        return [
            [false],
            [true],
        ];
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @dataProvider branchPruningModeDataProvider
     */
    public function testBranchPruning(bool $mode): void
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load(__DIR__ . '/../../data/Reader/XLSX/calculationTest.xlsx');
        $cal = Calculation::getInstance($spreadsheet);

        if(true === $mode){
            $cal->enableBranchPruning();
        }else {
            $cal->disableBranchPruning();
        }

        $spreadsheet->setActiveSheetIndexByName('Sheet1');

        $spreadsheet->getActiveSheet()->setCellValue('B1', 'AB10 0JB');
        if ($spreadsheet->getActiveSheet()->getCell('C1')->isFormula()) {
            $this->assertTrue($spreadsheet->getActiveSheet()->getCell('C1')->getCalculatedValue());
            $this->assertEquals(
                [
                    $spreadsheet->getActiveSheet()->getCell('K11')->getCalculatedValue(),
                    $spreadsheet->getActiveSheet()->getCell('K12')->getCalculatedValue(),
                    $spreadsheet->getActiveSheet()->getCell('K13')->getCalculatedValue(),
                ],
                [
                    '#N/A',
                    12,
                    '#N/A',
                ]
            );
        }
    }
}
