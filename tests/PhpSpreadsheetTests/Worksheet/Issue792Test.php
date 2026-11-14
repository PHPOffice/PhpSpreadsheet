<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\NamedFormula;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;
use ZipArchive;

class Issue792Test extends AbstractFunctional
{
    private string $outputFile = '';

    protected function tearDown(): void
    {
        if ($this->outputFile !== '') {
            unlink($this->outputFile);
            $this->outputFile = '';
        }
    }

    private static function createSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Sheet1');
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sheet2');
        $sheet1->setCellValue('A1', 10);
        $sheet2->setCellValue('A1', 20);
        $spreadsheet->addNamedRange(new NamedRange('GlobalOnSheet1', $sheet1, '$A$1'));
        $spreadsheet->addNamedRange(new NamedRange('LocalOnSheet1', $sheet1, '$A$1', true));
        $spreadsheet->addNamedRange(new NamedRange('GlobalOnSheet2', $sheet2, '$A$1'));
        $spreadsheet->addNamedRange(new NamedRange('LocalOnSheet2', $sheet2, '$A$1', true));

        return $spreadsheet;
    }

    public function testLocalNameIsRemovedWithItsSheet(): void
    {
        $spreadsheet = self::createSpreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        self::assertArrayNotHasKey('Sheet1!LOCALONSHEET1', $spreadsheet->getDefinedNames());
        $spreadsheet->disconnectWorksheets();
    }

    public function testGlobalNameReferringToRemovedSheetBecomesRefError(): void
    {
        $spreadsheet = self::createSpreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $definedName = $spreadsheet->getDefinedName('GlobalOnSheet1');
        self::assertNotNull($definedName);
        self::assertNull($definedName->getWorksheet());
        self::assertSame('#REF!', $definedName->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testNamesOnRemainingSheetAreUnchanged(): void
    {
        $spreadsheet = self::createSpreadsheet();
        $sheet2 = $spreadsheet->getSheetByNameOrThrow('Sheet2');
        $spreadsheet->removeSheetByIndex(0);

        $global = $spreadsheet->getDefinedName('GlobalOnSheet2');
        self::assertNotNull($global);
        self::assertSame($sheet2, $global->getWorksheet());
        self::assertSame('$A$1', $global->getValue());
        $local = $spreadsheet->getDefinedName('LocalOnSheet2', $sheet2);
        self::assertNotNull($local);
        self::assertSame($sheet2, $local->getScope());
        self::assertSame('$A$1', $local->getValue());
        self::assertCount(3, $spreadsheet->getDefinedNames());
        $spreadsheet->disconnectWorksheets();
    }

    public function testGlobalNamedFormulaIsUnchanged(): void
    {
        $spreadsheet = self::createSpreadsheet();
        $spreadsheet->addNamedFormula(new NamedFormula('Doubled', $spreadsheet->getSheet(0), '=Sheet2!$A$1*2'));
        $spreadsheet->removeSheetByIndex(0);

        $definedName = $spreadsheet->getDefinedName('Doubled');
        self::assertNotNull($definedName);
        self::assertSame('=Sheet2!$A$1*2', $definedName->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testRemovingSheetOfLoadedFileMatchesRoundTrip(): void
    {
        // Load a file whose names refer to a sheet, remove that sheet, then save it.
        $template = $this->writeAndReload(self::createSpreadsheet(), 'Xlsx');
        $template->removeSheetByIndex(0);
        $global = $template->getDefinedName('GlobalOnSheet1');
        self::assertNotNull($global);
        self::assertNull($global->getWorksheet());
        self::assertSame('#REF!', $global->getValue());
        self::assertNull($template->getDefinedName('LocalOnSheet1', $template->getSheet(0)));

        $reloaded = $this->writeAndReload($template, 'Xlsx');
        $template->disconnectWorksheets();

        self::assertSame(['Sheet2'], $reloaded->getSheetNames());
        $reloadedGlobal = $reloaded->getDefinedName('GlobalOnSheet1');
        self::assertNotNull($reloadedGlobal);
        self::assertNull($reloadedGlobal->getWorksheet());
        self::assertSame('#REF!', $reloadedGlobal->getValue());
        self::assertSame($global::class, $reloadedGlobal::class);
        $reloaded->disconnectWorksheets();
    }

    public function testXlsxDoesNotWriteReferenceToRemovedSheet(): void
    {
        $spreadsheet = self::createSpreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $zip = new ZipArchive();
        $open = $zip->open($this->outputFile, ZipArchive::RDONLY);
        self::assertTrue($open);
        $workbook = (string) $zip->getFromName('xl/workbook.xml');
        $zip->close();

        self::assertStringContainsString('<definedName name="GlobalOnSheet1">#REF!</definedName>', $workbook);
        self::assertStringNotContainsString('Sheet1', str_replace('GlobalOnSheet1', '', $workbook));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerType')]
    public function testNoSavedNameRefersToRemovedSheet(string $type): void
    {
        $spreadsheet = self::createSpreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $reloaded = $this->writeAndReload($spreadsheet, $type);
        $spreadsheet->disconnectWorksheets();

        self::assertSame(['Sheet2'], $reloaded->getSheetNames());
        foreach ($reloaded->getDefinedNames() as $definedName) {
            self::assertStringNotContainsString('Sheet1', $definedName->getValue());
        }
        $reloaded->disconnectWorksheets();
    }

    public static function providerType(): array
    {
        return [
            ['Xlsx'],
            ['Xls'],
            ['Ods'],
        ];
    }
}
