<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class Issue3126Test extends TestCase
{
    public function testReloadXlsxWorkbookProperties(): void
    {
        $filename = 'tests/data/Reader/XLSX/issue.3126.xlsx';
        $reader = new XlsxReader();
        $generation1 = $reader->load($filename);
        $gen1sheet = $generation1->getActiveSheet();
        $gen1hf = $gen1sheet->getHeaderFooter();
        $gen1Images = $gen1hf->getImages();
        self::assertCount(1, $gen1Images);
        $gen1hfName = array_key_exists('LF', $gen1Images) ? $gen1Images['LF']->getName() : '';
        self::assertSame('fleche-verte-up-right', $gen1hfName);
        $pageSetupRel = $generation1->getUnparsedLoadedData()['sheets']['Worksheet']['pageSetupRelId'] ?? '';
        self::assertSame('rId1ps', $pageSetupRel);
        $pageSetupPath = $generation1->getUnparsedLoadedData()['sheets']['Worksheet']['printerSettings'][substr($pageSetupRel, 3)]['filePath'] ?? '';
        self::assertSame('xl/printerSettings/printerSettings1.bin', $pageSetupPath);

        $generation2Name = File::temporaryFilename();
        $writer = new XlsxWriter($generation1);
        $writer->save($generation2Name);
        $generation1->disconnectWorksheets();
        $reader2 = new XlsxReader();
        $generation2 = $reader2->load($generation2Name);
        $gen2sheet = $generation2->getActiveSheet();
        $gen2hf = $gen2sheet->getHeaderFooter();
        $gen2Images = $gen2hf->getImages();
        self::assertCount(1, $gen2Images);
        $gen2hfName = array_key_exists('LF', $gen2Images) ? $gen2Images['LF']->getName() : '';
        self::assertSame('fleche-verte-up-right', $gen2hfName);
        $pageSetupRel = $generation2->getUnparsedLoadedData()['sheets']['Worksheet']['pageSetupRelId'] ?? '';
        self::assertSame('rId1ps', $pageSetupRel);
        $pageSetupPath = $generation2->getUnparsedLoadedData()['sheets']['Worksheet']['printerSettings'][substr($pageSetupRel, 3)]['filePath'] ?? '';
        self::assertSame('xl/printerSettings/printerSettings1.bin', $pageSetupPath);

        $generation3Name = File::temporaryFilename();
        $writer = new XlsxWriter($generation2);
        $writer->save($generation3Name);
        $generation2->disconnectWorksheets();
        $reader3 = new XlsxReader();
        $generation3 = $reader3->load($generation3Name);
        $gen3sheet = $generation3->getActiveSheet();
        $gen3hf = $gen3sheet->getHeaderFooter();
        $gen3Images = $gen3hf->getImages();
        self::assertCount(1, $gen3Images);
        $gen3hfName = array_key_exists('LF', $gen3Images) ? $gen3Images['LF']->getName() : '';
        self::assertSame('fleche-verte-up-right', $gen3hfName);
        $pageSetupRel = $generation3->getUnparsedLoadedData()['sheets']['Worksheet']['pageSetupRelId'] ?? '';
        self::assertSame('rId1ps', $pageSetupRel);
        $pageSetupPath = $generation3->getUnparsedLoadedData()['sheets']['Worksheet']['printerSettings'][substr($pageSetupRel, 3)]['filePath'] ?? '';
        self::assertSame('xl/printerSettings/printerSettings1.bin', $pageSetupPath);

        unlink($generation2Name);
        unlink($generation3Name);
        $generation3->disconnectWorksheets();
    }
}
