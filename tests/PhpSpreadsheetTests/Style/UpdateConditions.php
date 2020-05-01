<?php

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PHPUnit\Framework\TestCase;

class UpdateConditions extends TestCase
{
    public function testUpdateConditionsOneParameter()
    {
        $conditional = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional->addCondition('-E5');
        $makeFormula = '-E5';
        //Only change to formula
        self::assertEquals('=' . $makeFormula, $conditional->getUpdatedConditions()[0]);

        //Move Column 1 left with change to formula
        self::assertEquals('=-D5', $conditional->getUpdatedConditions(-1)[0]);

        //Move Column 1 right with change to formula
        self::assertEquals('=-F5', $conditional->getUpdatedConditions(1)[0]);

        //Move Row 1 up with change to formula
        self::assertEquals('=-E4', $conditional->getUpdatedConditions(0, -1)[0]);

        //Move Row 1 down with change to formula
        self::assertEquals('=-E6', $conditional->getUpdatedConditions(0, 1)[0]);

        //Move Column 1 left Row 1 up with change to formula
        self::assertEquals('=-D4', $conditional->getUpdatedConditions(-1, -1)[0]);

        //Move Column 1 left Row 1 down with change to formula
        self::assertEquals('=-D6', $conditional->getUpdatedConditions(-1, 1)[0]);

        //Move Column 1 right Row 1 up with change to formula
        self::assertEquals('=-F4', $conditional->getUpdatedConditions(1, -1)[0]);

        //Move Column 1 right Row 1 down with change to formula
        self::assertEquals('=-F6', $conditional->getUpdatedConditions(1, 1)[0]);

        //Move Column 1 left without change to formula
        self::assertEquals('-D5', $conditional->getUpdatedConditions(-1, 0, false)[0]);

        //Move Column 1 right without change to formula
        self::assertEquals('-F5', $conditional->getUpdatedConditions(1, 0, false)[0]);

        //Move Row 1 up without change to formula
        self::assertEquals('-E4', $conditional->getUpdatedConditions(0, -1, false)[0]);

        //Move Row 1 down without change to formula
        self::assertEquals('-E6', $conditional->getUpdatedConditions(0, 1, false)[0]);

        //Move Column 1 left Row 1 up without change to formula
        self::assertEquals('-D4', $conditional->getUpdatedConditions(-1, -1, false)[0]);

        //Move Column 1 left Row 1 down without change to formula
        self::assertEquals('-D6', $conditional->getUpdatedConditions(-1, 1, false)[0]);

        //Move Column 1 right Row 1 up without change to formula
        self::assertEquals('-F4', $conditional->getUpdatedConditions(1, -1, false)[0]);

        //Move Column 1 right Row 1 down without change to formula
        self::assertEquals('-F6', $conditional->getUpdatedConditions(1, 1, false)[0]);

        //No changes
        self::assertEquals($makeFormula, $conditional->getUpdatedConditions(0, 0, false)[0]);
    }

    public function testUpdateConditionsTwoParameter()
    {
        $conditional = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional->addCondition('-E5');
        $conditional->addCondition('E5');
        $makeFormula = '-E5';
        $makeFormula1 = 'E5';
        //Only change to formula
        self::assertEquals('=' . $makeFormula, $conditional->getUpdatedConditions()[0]);
        self::assertEquals('=' . $makeFormula1, $conditional->getUpdatedConditions()[1]);

        //Move Column 1 left with change to formula
        self::assertEquals('=-D5', $conditional->getUpdatedConditions(-1)[0]);
        self::assertEquals('=D5', $conditional->getUpdatedConditions(-1)[1]);

        //Move Column 1 right with change to formula
        self::assertEquals('=-F5', $conditional->getUpdatedConditions(1)[0]);
        self::assertEquals('=F5', $conditional->getUpdatedConditions(1)[1]);

        //Move Row 1 up with change to formula
        self::assertEquals('=-E4', $conditional->getUpdatedConditions(0, -1)[0]);
        self::assertEquals('=E4', $conditional->getUpdatedConditions(0, -1)[1]);

        //Move Row 1 down with change to formula
        self::assertEquals('=-E6', $conditional->getUpdatedConditions(0, 1)[0]);
        self::assertEquals('=E6', $conditional->getUpdatedConditions(0, 1)[1]);

        //Move Column 1 left Row 1 up with change to formula
        self::assertEquals('=-D4', $conditional->getUpdatedConditions(-1, -1)[0]);
        self::assertEquals('=D4', $conditional->getUpdatedConditions(-1, -1)[1]);

        //Move Column 1 left Row 1 down with change to formula
        self::assertEquals('=-D6', $conditional->getUpdatedConditions(-1, 1)[0]);
        self::assertEquals('=D6', $conditional->getUpdatedConditions(-1, 1)[1]);

        //Move Column 1 right Row 1 up with change to formula
        self::assertEquals('=-F4', $conditional->getUpdatedConditions(1, -1)[0]);
        self::assertEquals('=F4', $conditional->getUpdatedConditions(1, -1)[1]);

        //Move Column 1 right Row 1 down with change to formula
        self::assertEquals('=-F6', $conditional->getUpdatedConditions(1, 1)[0]);
        self::assertEquals('=F6', $conditional->getUpdatedConditions(1, 1)[1]);

        //Move Column 1 left without change to formula
        self::assertEquals('-D5', $conditional->getUpdatedConditions(-1, 0, false)[0]);
        self::assertEquals('D5', $conditional->getUpdatedConditions(-1, 0, false)[1]);

        //Move Column 1 right without change to formula
        self::assertEquals('-F5', $conditional->getUpdatedConditions(1, 0, false)[0]);
        self::assertEquals('F5', $conditional->getUpdatedConditions(1, 0, false)[1]);

        //Move Row 1 up without change to formula
        self::assertEquals('-E4', $conditional->getUpdatedConditions(0, -1, false)[0]);
        self::assertEquals('E4', $conditional->getUpdatedConditions(0, -1, false)[1]);

        //Move Row 1 down without change to formula
        self::assertEquals('-E6', $conditional->getUpdatedConditions(0, 1, false)[0]);
        self::assertEquals('E6', $conditional->getUpdatedConditions(0, 1, false)[1]);

        //Move Column 1 left Row 1 up without change to formula
        self::assertEquals('-D4', $conditional->getUpdatedConditions(-1, -1, false)[0]);
        self::assertEquals('D4', $conditional->getUpdatedConditions(-1, -1, false)[1]);

        //Move Column 1 left Row 1 down without change to formula
        self::assertEquals('-D6', $conditional->getUpdatedConditions(-1, 1, false)[0]);
        self::assertEquals('D6', $conditional->getUpdatedConditions(-1, 1, false)[1]);

        //Move Column 1 right Row 1 up without change to formula
        self::assertEquals('-F4', $conditional->getUpdatedConditions(1, -1, false)[0]);
        self::assertEquals('F4', $conditional->getUpdatedConditions(1, -1, false)[1]);

        //Move Column 1 right Row 1 down without change to formula
        self::assertEquals('-F6', $conditional->getUpdatedConditions(1, 1, false)[0]);
        self::assertEquals('F6', $conditional->getUpdatedConditions(1, 1, false)[1]);

        //No changes
        self::assertEquals($makeFormula, $conditional->getUpdatedConditions(0, 0, false)[0]);
        self::assertEquals($makeFormula1, $conditional->getUpdatedConditions(0, 0, false)[1]);
    }
}
