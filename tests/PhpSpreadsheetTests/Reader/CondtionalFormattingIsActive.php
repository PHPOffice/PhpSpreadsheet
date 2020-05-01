<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class CondtionalFormattingIsActive extends TestCase
{
    public function testCondtionalformattingIsActive()
    {
        $filename = './data/Reader/XLSX/ConditionalFormattingIsActiveTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();
        $conditionalFormattings = $worksheet->getConditionalStylesCollection();
        $calcer = Calculation::getInstance($spreadsheet);
		$calcer->disableCalculationCache();
        $precision = 8;

        // Taken from Excel 2013 manualy
        $should = [false, true, true, false, true, false, false, true, true, false, false, true, true, false, false, true, true, false, true, false, false, false, true, true];

        $actual = [];

        self::assertTrue(isset($conditionalFormattings));
        self::assertTrue(count($conditionalFormattings) > 0);

        foreach ($conditionalFormattings as $key => $formatings) {
            $split = explode(':', $key);
            $col = ord(substr($split[0], 0, 1));
            $multuseCol = false;

            if (count($split) > 1) {
                $colEnd = ord(substr($split[0], 0, 1));
            } else {
                $colEnd = $col;
            }

            $row = substr($split[0], 1);

            if (count($split) > 1) {
                $rowEnd = substr($split[1], 1);
            } else {
                $rowEnd = $row;
            }

            $multuseRow = false;
            for ($i = $col; $i <= $colEnd; ++$i) {
                for ($j = $row; $j <= $rowEnd; ++$j) {
                    if (isset($formatings) && count($formatings) > 0) {
                        foreach ($formatings as $formating) {
                            if ($col != $colEnd) {
                                $multuseCol = ($col - $i) * (-1);
                            }
                            if ($row != $rowEnd) {
                                $multuseRow = ($row - $j) * (-1);
                            }

                            $cell = $worksheet->getCell(chr($i) . $j);
							
                            $active = $formating->isActive($calcer, $cell, $precision, $multuseCol, $multuseRow);
                            $actual[$j - 1] = $active;
                        }
                    }
                }
            }
        }
        $count = count($should);
        for ($i = 0; $i < $count; ++$i) {
            self::assertEquals($should[$i], $actual[$i]);
			
        }
    }
}
