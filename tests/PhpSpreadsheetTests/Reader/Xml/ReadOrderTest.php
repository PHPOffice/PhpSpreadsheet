<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml as XmlReader;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ReadOrderTest extends AbstractFunctional
{
    public function testReadOrder(): void
    {
        // Issue 850 - Xls Reader/Writer didn't support Alignment ReadOrder
        $infile = 'tests/data/Reader/Xml/issue.850.xml';
        $reader = new XmlReader();
        $robj = $reader->load($infile);

        $sheet0 = $robj->setActiveSheetIndex(0);
        self::assertSame(
            Alignment::READORDER_RTL,
            $sheet0->getStyle('A1')->getAlignment()->getReadOrder()
        );
        self::assertSame(
            Alignment::READORDER_LTR,
            $sheet0->getStyle('A2')->getAlignment()->getReadOrder()
        );
        self::assertSame(
            Alignment::READORDER_CONTEXT,
            $sheet0->getStyle('A3')->getAlignment()->getReadOrder()
        );
        self::assertSame(
            2,
            $sheet0->getStyle('A5')->getAlignment()->getIndent()
        );
        $robj->disconnectWorksheets();
    }
}
