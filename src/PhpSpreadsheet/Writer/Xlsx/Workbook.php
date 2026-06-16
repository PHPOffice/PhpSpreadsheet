<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\DefinedNames as DefinedNamesWriter;

class Workbook extends WriterPart
{
    /**
     * Write workbook to XML format.
     *
     * @param bool $preCalculateFormulas If true, formulas will be calculated before writing
     * @param ?bool $forceFullCalc If null, !$preCalculateFormulas
     *
     * @return string XML Output
     */
    public function writeWorkbook(Spreadsheet $spreadsheet, bool $preCalculateFormulas = false, ?bool $forceFullCalc = null): string
    {
        // Create XML writer
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // workbook
        $objWriter->startElement('workbook');
        $objWriter->writeAttribute('xmlns', Namespaces::MAIN);
        $objWriter->writeAttribute('xmlns:r', Namespaces::SCHEMA_OFFICE_DOCUMENT);

        // fileVersion
        $this->writeFileVersion($objWriter);

        // workbookPr
        $this->writeWorkbookPr($objWriter, $spreadsheet);

        // workbookProtection
        $this->writeWorkbookProtection($objWriter, $spreadsheet);

        // bookViews
        if ($this->getParentWriter()->getOffice2003Compatibility() === false) {
            $this->writeBookViews($objWriter, $spreadsheet);
        }

        // sheets
        $this->writeSheets($objWriter, $spreadsheet);

        // definedNames
        (new DefinedNamesWriter($objWriter, $spreadsheet))->write();

        // calcPr
        $this->writeCalcPr($objWriter, $preCalculateFormulas, $forceFullCalc);

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write file version.
     */
    private function writeFileVersion(XMLWriter $objWriter): void
    {
        $objWriter->startElement('fileVersion');
        $objWriter->writeAttribute('appName', 'xl');
        $objWriter->writeAttribute('lastEdited', '4');
        $objWriter->writeAttribute('lowestEdited', '4');
        $objWriter->writeAttribute('rupBuild', '4505');
        $objWriter->endElement();
    }

    /**
     * Write WorkbookPr.
     */
    private function writeWorkbookPr(XMLWriter $objWriter, Spreadsheet $spreadsheet): void
    {
        $objWriter->startElement('workbookPr');

        if ($spreadsheet->getExcelCalendar() === Date::CALENDAR_MAC_1904) {
            $objWriter->writeAttribute('date1904', '1');
        }

        $objWriter->writeAttribute('codeName', 'ThisWorkbook');

        $objWriter->endElement();
    }

    /**
     * Write BookViews.
     */
    private function writeBookViews(XMLWriter $objWriter, Spreadsheet $spreadsheet): void
    {
        // bookViews
        $objWriter->startElement('bookViews');

        // workbookView
        $objWriter->startElement('workbookView');

        $objWriter->writeAttribute('activeTab', (string) $spreadsheet->getActiveSheetIndex());
        $objWriter->writeAttribute('autoFilterDateGrouping', ($spreadsheet->getAutoFilterDateGrouping() ? 'true' : 'false'));
        $objWriter->writeAttribute('firstSheet', (string) $spreadsheet->getFirstSheetIndex());
        $objWriter->writeAttribute('minimized', ($spreadsheet->getMinimized() ? 'true' : 'false'));
        $objWriter->writeAttribute('showHorizontalScroll', ($spreadsheet->getShowHorizontalScroll() ? 'true' : 'false'));
        $objWriter->writeAttribute('showSheetTabs', ($spreadsheet->getShowSheetTabs() ? 'true' : 'false'));
        $objWriter->writeAttribute('showVerticalScroll', ($spreadsheet->getShowVerticalScroll() ? 'true' : 'false'));
        $objWriter->writeAttribute('tabRatio', (string) $spreadsheet->getTabRatio());
        $objWriter->writeAttribute('visibility', $spreadsheet->getVisibility());

        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write WorkbookProtection.
     */
    private function writeWorkbookProtection(XMLWriter $objWriter, Spreadsheet $spreadsheet): void
    {
        $security = $spreadsheet->getSecurity();
        if ($security->isSecurityEnabled()) {
            $objWriter->startElement('workbookProtection');
            $objWriter->writeAttribute('lockRevision', ($security->getLockRevision() ? 'true' : 'false'));
            $objWriter->writeAttribute('lockStructure', ($security->getLockStructure() ? 'true' : 'false'));
            $objWriter->writeAttribute('lockWindows', ($security->getLockWindows() ? 'true' : 'false'));

            if ($security->getRevisionsPassword() !== '') {
                $objWriter->writeAttribute('revisionsPassword', $security->getRevisionsPassword());
            } else {
                $hashValue = $security->getRevisionsHashValue();
                if ($hashValue !== '') {
                    $objWriter->writeAttribute('revisionsAlgorithmName', $security->getRevisionsAlgorithmName());
                    $objWriter->writeAttribute('revisionsHashValue', $hashValue);
                    $objWriter->writeAttribute('revisionsSaltValue', $security->getRevisionsSaltValue());
                    $objWriter->writeAttribute('revisionsSpinCount', (string) $security->getRevisionsSpinCount());
                }
            }

            if ($security->getWorkbookPassword() !== '') {
                $objWriter->writeAttribute('workbookPassword', $security->getWorkbookPassword());
            } else {
                $hashValue = $security->getWorkbookHashValue();
                if ($hashValue !== '') {
                    $objWriter->writeAttribute('workbookAlgorithmName', $security->getWorkbookAlgorithmName());
                    $objWriter->writeAttribute('workbookHashValue', $hashValue);
                    $objWriter->writeAttribute('workbookSaltValue', $security->getWorkbookSaltValue());
                    $objWriter->writeAttribute('workbookSpinCount', (string) $security->getWorkbookSpinCount());
                }
            }

            $objWriter->endElement();
        }
    }

    /**
     * Write calcPr.
     *
     * @param bool $preCalculateFormulas If true, formulas will be calculated before writing
     */
    private function writeCalcPr(XMLWriter $objWriter, bool $preCalculateFormulas, ?bool $forceFullCalc): void
    {
        $objWriter->startElement('calcPr');

        //    Set the calcid to a higher value than Excel itself will use, otherwise Excel will always recalc
        //  If MS Excel does do a recalc, then users opening a file in MS Excel will be prompted to save on exit
        //     because the file has changed
        $objWriter->writeAttribute('calcId', '999999');
        $objWriter->writeAttribute('calcMode', 'auto');
        //    fullCalcOnLoad isn't needed if we will calculate before writing
        $objWriter->writeAttribute('calcCompleted', ($preCalculateFormulas) ? '1' : '0');
        $objWriter->writeAttribute('fullCalcOnLoad', ($preCalculateFormulas) ? '0' : '1');
        if ($forceFullCalc === null) {
            $objWriter->writeAttribute('forceFullCalc', $preCalculateFormulas ? '0' : '1');
        } else {
            $objWriter->writeAttribute('forceFullCalc', $forceFullCalc ? '1' : '0');
        }

        $objWriter->endElement();
    }

    /**
     * Write sheets.
     */
    private function writeSheets(XMLWriter $objWriter, Spreadsheet $spreadsheet): void
    {
        // Write sheets
        $objWriter->startElement('sheets');
        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            // sheet
            $this->writeSheet(
                $objWriter,
                $spreadsheet->getSheet($i)->getTitle(),
                ($i + 1),
                ($i + 1 + 3),
                $spreadsheet->getSheet($i)->getSheetState()
            );
        }

        $objWriter->endElement();
    }

    /**
     * Write sheet.
     *
     * @param string $worksheetName Sheet name
     * @param int $worksheetId Sheet id
     * @param int $relId Relationship ID
     * @param string $sheetState Sheet state (visible, hidden, veryHidden)
     */
    private function writeSheet(XMLWriter $objWriter, string $worksheetName, int $worksheetId = 1, int $relId = 1, string $sheetState = 'visible'): void
    {
        if ($worksheetName != '') {
            // Write sheet
            $objWriter->startElement('sheet');
            $objWriter->writeAttribute('name', $worksheetName);
            $objWriter->writeAttribute('sheetId', (string) $worksheetId);
            if ($sheetState !== 'visible' && $sheetState != '') {
                $objWriter->writeAttribute('state', $sheetState);
            }
            $objWriter->writeAttribute('r:id', 'rId' . $relId);
            $objWriter->endElement();
        } else {
            throw new WriterException('Invalid parameters passed.');
        }
    }
}
