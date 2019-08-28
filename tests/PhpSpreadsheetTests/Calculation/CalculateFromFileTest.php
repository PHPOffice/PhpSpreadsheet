<?php declare (strict_types = 1);

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class CalculateFromFileTest extends TestCase
{

    public function sampleDataProvider(): array
    {
        return  [
            ['AL6 0JB', TRUE, [15,16,22]],
        ];
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function testBranchPruningOff(): void
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load(__DIR__ . '/../../data/Reader/XLSX/calculationTest.xlsx');

        $spreadsheet->setActiveSheetIndexByName('Sheet1');
        $spreadsheet->getActiveSheet()->setCellValue('B1', 'AL6 0JB');
        if ($spreadsheet->getActiveSheet()->getCell('C1')->isFormula()) {
            $this->assertEquals(true, $spreadsheet->getActiveSheet()->getCell('C1')->getCalculatedValue());
            $spreadsheet->setActiveSheetIndexByName('Sheet2');
            $this->assertEquals([$spreadsheet->getActiveSheet()->getCell('K11')->getCalculatedValue(),
                $spreadsheet->getActiveSheet()->getCell('K12')->getCalculatedValue(),
                $spreadsheet->getActiveSheet()->getCell('K13')->getCalculatedValue()], [15,16,22]);
        }
    }


    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function testBranchPruningOn(): void
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load(__DIR__ . '/../../data/Reader/XLSX/calculationTest.xlsx');
        // turn on branch pruning
        $cal = Calculation::getInstance($spreadsheet);
        $cal->enableBranchPruning();

        $spreadsheet->setActiveSheetIndexByName('Sheet1');
        $spreadsheet->getActiveSheet()->setCellValue('B1', 'AL6 0JB');
        if ($spreadsheet->getActiveSheet()->getCell('C1')->isFormula()) {
            $this->assertEquals(false, $spreadsheet->getActiveSheet()->getCell('C1')->getCalculatedValue());
            $spreadsheet->setActiveSheetIndexByName('Sheet2');
            $this->assertEquals([$spreadsheet->getActiveSheet()->getCell('K11')->getCalculatedValue(),
                $spreadsheet->getActiveSheet()->getCell('K12')->getCalculatedValue(),
                $spreadsheet->getActiveSheet()->getCell('K13')->getCalculatedValue()], ['Pruned branch (only if storeKey-2) K3','Pruned branch (only if storeKey-3) K7','Pruned branch (only if storeKey-4) K9']);
        }
    }
}
