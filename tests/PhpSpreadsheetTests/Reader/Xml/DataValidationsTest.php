<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DataValidationsTest extends AbstractFunctional
{
    private string $filename = 'tests/data/Reader/Xml/datavalidations.xml';

    public function testValidation(): void
    {
        $reader = new Xml();
        $spreadsheet = $reader->load($this->filename);
        $sheet = $spreadsheet->getActiveSheet();
        $assertions = $this->validationAssertions();
        $validation = $sheet->getCell('A1')->getDataValidation();
        self::assertSame('A1:A1048576', $validation->getSqref());
        $validation = $sheet->getCell('B3')->getDataValidation();
        self::assertSame('B2:B1048576', $validation->getSqref());

        foreach ($assertions as $title => $assertion) {
            $sheet->getCell($assertion[1])->setValue($assertion[2]);
            self::assertSame($assertion[0], $sheet->getCell($assertion[1])->hasValidValue(), $title);
        }
        $sheet->getCell('F1')->getDataValidation()->setType(DataValidation::TYPE_NONE);
        $sheet->getCell('F1')->setValue(1);
        self::assertTrue($sheet->getCell('F1')->hasValidValue(), 'validation type is NONE');
        $spreadsheet->disconnectWorksheets();
    }

    public function testValidationXlsx(): void
    {
        $reader = new Xml();
        $oldspreadsheet = $reader->load($this->filename);
        $spreadsheet = $this->writeAndReload($oldspreadsheet, 'Xlsx');
        $oldspreadsheet->disconnectWorksheets();

        $sheet = $spreadsheet->getActiveSheet();
        $assertions = $this->validationAssertions();
        $validation = $sheet->getCell('A1')->getDataValidation();
        self::assertSame('A1:A1048576', $validation->getSqref());
        $validation = $sheet->getCell('B3')->getDataValidation();
        self::assertSame('B2:B1048576', $validation->getSqref());

        foreach ($assertions as $title => $assertion) {
            $sheet->getCell($assertion[1])->setValue($assertion[2]);
            self::assertSame($assertion[0], $sheet->getCell($assertion[1])->hasValidValue(), $title);
        }
        $spreadsheet->disconnectWorksheets();
    }

    public function testValidationXls(): void
    {
        $reader = new Xml();
        $oldspreadsheet = $reader->load($this->filename);
        $spreadsheet = $this->writeAndReload($oldspreadsheet, 'Xls');
        $oldspreadsheet->disconnectWorksheets();

        $sheet = $spreadsheet->getActiveSheet();
        $assertions = $this->validationAssertions();
        //$validation = $sheet->getCell('A1')->getDataValidation();
        //self::assertSame('A1:A1048576', $validation->getSqref());
        //$validation = $sheet->getCell('B3')->getDataValidation();
        //self::assertSame('B2:B1048576', $validation->getSqref());

        foreach ($assertions as $title => $assertion) {
            $sheet->getCell($assertion[1])->setValue($assertion[2]);
            self::assertSame($assertion[0], $sheet->getCell($assertion[1])->hasValidValue(), $title);
        }
        $spreadsheet->disconnectWorksheets();
    }

    private function validationAssertions(): array
    {
        return [
            // Numeric tests
            'Integer between 2 and 5: x' => [false, 'F1', 'x'],
            'Integer between 2 and 5: 3.1' => [false, 'F1', 3.1],
            'Integer between 2 and 5: 3' => [true, 'F1', 3],
            'Integer between 2 and 5: 1' => [false, 'F1', 1],
            'Integer between 2 and 5: 7' => [false, 'F1', 7],
            'Float between 2 and 5: x' => [false, 'G1', 'x'],
            'Float between 2 and 5: 3.1' => [true, 'G1', 3.1],
            'Float between 2 and 5: 3' => [true, 'G1', 3],
            'Float between 2 and 5: 1' => [false, 'G1', 1],
            'Float between 2 and 5: 7' => [false, 'G1', 7],
            'Integer not between -5 and 5: 3' => [false, 'F2', 3],
            'Integer not between -5 and 5: -1' => [false, 'F2', -1],
            'Integer not between -5 and 5: 7' => [true, 'F2', 7],
            'Any integer except 7: -1' => [true, 'F3', -1],
            'Any integer except 7: 7' => [false, 'F3', 7],
            'Only -3: -1' => [false, 'F4', -1],
            'Only -3: -3' => [true, 'F4', -3],
            'Integer less than 8: 8' => [false, 'F5', 8],
            'Integer less than 8: 7' => [true, 'F5', 7],
            'Integer less than 8: 9' => [false, 'F5', 9],
            'Integer less than or equal 12: 12' => [true, 'F6', 12],
            'Integer less than or equal 12: 7' => [true, 'F6', 7],
            'Integer less than or equal 12: 13' => [false, 'F6', 13],
            'Integer greater than or equal -6: -6' => [true, 'F7', -6],
            'Integer greater than or equal -6: -7' => [false, 'F7', -7],
            'Integer greater than or equal -6: -5' => [true, 'F7', -5],
            'Integer greater than 5: 5' => [false, 'F8', 5],
            'Integer greater than 5: 6' => [true, 'F8', 6],
            'Integer greater than 5: 3' => [false, 'F8', 3],
            // Text tests
            'a,b,c,d,e: a' => [true, 'C4', 'a'],
            'a,b,c,d,e: c' => [true, 'C4', 'c'],
            'a,b,c,d,e: e' => [true, 'C4', 'e'],
            'a,b,c,d,e: x' => [false, 'C4', 'x'],
            'a,b,c,d,e: aa' => [false, 'C4', 'aa'],
            'less than 8 characters: abcdefg' => [true, 'C3', 'abcdefg'],
            'less than 8 characters: abcdefgh' => [false, 'C3', 'abcdefgh'],
            'texts in e1 to e5: ccc' => [true, 'D2', 'ccc'],
            'texts in e1 to e5: ffffff' => [false, 'D2', 'ffffff'],
            'date from 20230101: 20221231' => [false, 'C1', Date::convertIsoDate('20221231')],
            'date from 20230101: 20230101' => [true, 'C1', Date::convertIsoDate('20230101')],
            'date from 20230101: 20240507' => [true, 'C1', Date::convertIsoDate('20240507')],
            'date from 20230101: 20240507 10:00:00' => [true, 'C1', Date::convertIsoDate('20240507 10:00:00')],
            'time from 12:00-14:00: 2023-01-01 13:00:00' => [false, 'C2', Date::convertIsoDate('2023-01-01 13:00:00')],
            'time from 8:00-14:00: 13:00' => [true, 'C2', Date::convertIsoDate('13:00')],
            'time from 8:00-14:00: 07:00:00' => [false, 'C2', Date::convertIsoDate('07:00:00')],
            'time from 8:00-14:00: 15:00:00' => [false, 'C2', Date::convertIsoDate('15:00:00')],
            'time from 8:00-14:00: 1:13 am' => [false, 'C2', Date::convertIsoDate('1:13 am')],
            'time from 8:00-14:00: 1:13 pm' => [true, 'C2', Date::convertIsoDate('1:13 pm')],
            'time from 8:00-14:00: 9:13' => [true, 'C2', Date::convertIsoDate('9:13')],
            'time from 8:00-14:00: 9:13 am' => [true, 'C2', Date::convertIsoDate('9:13 am')],
            'time from 8:00-14:00: 9:13 pm' => [false, 'C2', Date::convertIsoDate('9:13 pm')],
        ];
    }
}
