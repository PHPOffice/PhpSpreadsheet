<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class RefErrorTest extends TestCase
{
    /**
     * @dataProvider providerRefError
     */
    public function testRefError(mixed $expected, string $formula): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Sheet1');
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sheet2');
        $sheet2->getCell('A1')->setValue(5);
        $sheet1->getCell('A1')->setValue(9);
        $sheet1->getCell('A2')->setValue(2);
        $sheet1->getCell('A3')->setValue(4);
        $sheet1->getCell('A4')->setValue(6);
        $sheet1->getCell('A5')->setValue(7);
        $sheet1->getRowDimension(5)->setVisible(false);
        $sheet1->getCell('B1')->setValue('=1/0');
        $sheet1->getCell('C1')->setValue('=Sheet99!A1');
        $sheet1->getCell('C2')->setValue('=Sheet2!A1');
        $sheet1->getCell('C3')->setValue('=Sheet2!A2');
        $sheet1->getCell('H1')->setValue($formula);
        self::assertSame($expected, $sheet1->getCell('H1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerRefError(): array
    {
        return [
            'Subtotal9 Ok' => [12, '=SUBTOTAL(A1,A2:A4)'],
            'Subtotal9 REF' => ['#REF!', '=SUBTOTAL(A1,A2:A4,C1)'],
            'Subtotal9 with literal and cells' => [111, '=SUBTOTAL(A1,A2:A4,99)'],
            'Subtotal9 with literal no rows hidden' => [111, '=SUBTOTAL(109,A2:A4,99)'],
            'Subtotal9 with literal ignoring hidden row' => [111, '=SUBTOTAL(109,A2:A5,99)'],
            'Subtotal9 with literal using hidden row' => [118, '=SUBTOTAL(9,A2:A5,99)'],
            'Subtotal9 with Null same sheet' => [12, '=SUBTOTAL(A1,A2:A4,A99)'],
            'Subtotal9 with Null Different sheet' => [12, '=SUBTOTAL(A1,A2:A4,C3)'],
            'Subtotal9 with NonNull Different sheet' => [17, '=SUBTOTAL(A1,A2:A4,C2)'],
            'Product DIV0' => ['#DIV/0!', '=PRODUCT(2, 3, B1)'],
            'Sqrt REF' => ['#REF!', '=SQRT(C1)'],
            'Sum NUM' => ['#NUM!', '=SUM(SQRT(-1), A2:A4)'],
            'Sum with literal and cells' => [111, '=SUM(A2:A4, 99)'],
            'Sum REF' => ['#REF!', '=SUM(A2:A4, C1)'],
            'Tan DIV0' => ['#DIV/0!', '=TAN(B1)'],
        ];
    }
}
