<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\DataValidator;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class NonLatinFormulasTest extends AbstractFunctional
{
    public function testNonLatin(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $validation = $worksheet->getCell('B1')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1('"слово, сло"');

        $dataValidator = new DataValidator();
        $worksheet->getCell('B1')->setValue('слово');
        self::assertTrue(
            $dataValidator->isValid($worksheet->getCell('B1'))
        );
        $worksheet->getCell('B1')->setValue('слов');
        self::assertFalse(
            $dataValidator->isValid($worksheet->getCell('B1'))
        );

        $worksheet->setTitle('словслов');
        $worksheet->getCell('A1')->setValue('=словслов!B1');
        $worksheet->getCell('A2')->setValue("='словслов'!B1");
        $spreadsheet->addNamedRange(new NamedRange('слсл', $worksheet, '$B$1'));
        $worksheet->getCell('A3')->setValue('=слсл');

        $robj = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $sheet0 = $robj->getActiveSheet();
        self::assertSame('словслов', $sheet0->getTitle());
        self::assertSame('=словслов!B1', $sheet0->getCell('A1')->getValue());
        self::assertSame('слов', $sheet0->getCell('A1')->getCalculatedValue());
        // Quotes around sheet name are stripped off - harmless
        //self::assertSame("='словслов'!B1", $sheet0->getCell('A2')->getValue());
        self::assertSame('слов', $sheet0->getCell('A2')->getCalculatedValue());
        // Formulas with defined names don't work in Xls Writer
        //self::assertSame('=слсл', $sheet0->getCell('A3')->getValue());
        // But result should be accurate
        self::assertSame('слов', $sheet0->getCell('A3')->getCalculatedValue());
        $names = $robj->getDefinedNames();
        self::assertCount(1, $names);
        // name has been uppercased
        $namedRange = $names['СЛСЛ'] ?? null;
        self::assertInstanceOf(NamedRange::class, $namedRange);
        self::assertSame('$B$1', $namedRange->getRange());

        $robj->disconnectWorksheets();
    }
}
