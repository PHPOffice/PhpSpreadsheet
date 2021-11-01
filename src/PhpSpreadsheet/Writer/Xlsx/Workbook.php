<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
     * @param bool $recalcRequired Indicate whether formulas should be recalculated before writing
     *
     * @return string XML Output
     */
    public function writeWorkbook(Spreadsheet $spreadsheet, $recalcRequired = false)
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
        $objWriter->writeAttribute('xml:space', 'preserve');
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $objWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

        // fileVersion
        $this->writeFileVersion($objWriter);

        // workbookPr
        $this->writeWorkbookPr($objWriter);

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
        $this->writeCalcPr($objWriter, $recalcRequired);

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write file version.
     *
     * @param XMLWriter $objWriter XML Writer
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
     *
     * @param XMLWriter $objWriter XML Writer
     */
    private function writeWorkbookPr(XMLWriter $objWriter): void
    {
        $objWriter->startElement('workbookPr');

        if (Date::getExcelCalendar() === Date::CALENDAR_MAC_1904) {
            $objWriter->writeAttribute('date1904', '1');
        }

        $objWriter->writeAttribute('codeName', 'ThisWorkbook');

        $objWriter->endElement();
    }

    /**
     * Write BookViews.
     *
     * @param XMLWriter $objWriter XML Writer
     */
    private function writeBookViews(XMLWriter $objWriter, Spreadsheet $spreadsheet): void
    {
        // bookViews
        $objWriter->startElement('bookViews');

        // workbookView
        $objWriter->startElement('workbookView');

        $objWriter->writeAttribute('activeTab', $spreadsheet->getActiveSheetIndex());
        $objWriter->writeAttribute('autoFilterDateGrouping', ($spreadsheet->getAutoFilterDateGrouping() ? 'true' : 'false'));
        $objWriter->writeAttribute('firstSheet', $spreadsheet->getFirstSheetIndex());
        $objWriter->writeAttribute('minimized', ($spreadsheet->getMinimized() ? 'true' : 'false'));
        $objWriter->writeAttribute('showHorizontalScroll', ($spreadsheet->getShowHorizontalScroll() ? 'true' : 'false'));
        $objWriter->writeAttribute('showSheetTabs', ($spreadsheet->getShowSheetTabs() ? 'true' : 'false'));
        $objWriter->writeAttribute('showVerticalScroll', ($spreadsheet->getShowVerticalScroll() ? 'true' : 'false'));
        $objWriter->writeAttribute('tabRatio', $spreadsheet->getTabRatio());
        $objWriter->writeAttribute('visibility', $spreadsheet->getVisibility());

        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write WorkbookProtection.
     *
     * @param XMLWriter $objWriter XML Writer
     */
    private function writeWorkbookProtection(XMLWriter $objWriter, Spreadsheet $spreadsheet): void
    {
        if ($spreadsheet->getSecurity()->isSecurityEnabled()) {
            $objWriter->startElement('workbookProtection');
            $objWriter->writeAttribute('lockRevision', ($spreadsheet->getSecurity()->getLockRevision() ? 'true' : 'false'));
            $objWriter->writeAttribute('lockStructure', ($spreadsheet->getSecurity()->getLockStructure() ? 'true' : 'false'));
            $objWriter->writeAttribute('lockWindows', ($spreadsheet->getSecurity()->getLockWindows() ? 'true' : 'false'));

            if ($spreadsheet->getSecurity()->getRevisionsPassword() != '') {
                $objWriter->writeAttribute('revisionsPassword', $spreadsheet->getSecurity()->getRevisionsPassword());
            }

            if ($spreadsheet->getSecurity()->getWorkbookPassword() != '') {
                $objWriter->writeAttribute('workbookPassword', $spreadsheet->getSecurity()->getWorkbookPassword());
            }

            $objWriter->endElement();
        }
    }

    /**
     * Write calcPr.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param bool $recalcRequired Indicate whether formulas should be recalculated before writing
     */
    private function writeCalcPr(XMLWriter $objWriter, $recalcRequired = true): void
    {
        $objWriter->startElement('calcPr');

        //    Set the calcid to a higher value than Excel itself will use, otherwise Excel will always recalc
        //  If MS Excel does do a recalc, then users opening a file in MS Excel will be prompted to save on exit
        //     because the file has changed
        $objWriter->writeAttribute('calcId', '999999');
        $objWriter->writeAttribute('calcMode', 'auto');
        //    fullCalcOnLoad isn't needed if we've recalculating for the save
        $objWriter->writeAttribute('calcCompleted', ($recalcRequired) ? 1 : 0);
        $objWriter->writeAttribute('fullCalcOnLoad', ($recalcRequired) ? 0 : 1);
        $objWriter->writeAttribute('forceFullCalc', ($recalcRequired) ? 0 : 1);

        $objWriter->endElement();
    }

    /**
     * Write sheets.
     *
     * @param XMLWriter $objWriter XML Writer
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
     * @param XMLWriter $objWriter XML Writer
     * @param string $worksheetName Sheet name
     * @param int $worksheetId Sheet id
     * @param int $relId Relationship ID
     * @param string $sheetState Sheet state (visible, hidden, veryHidden)
     */
    private function writeSheet(XMLWriter $objWriter, $worksheetName, $worksheetId = 1, $relId = 1, $sheetState = 'visible'): void
    {
        if ($worksheetName != '') {
            // Write sheet
            $objWriter->startElement('sheet');
            $objWriter->writeAttribute('name', $worksheetName);
            $objWriter->writeAttribute('sheetId', $worksheetId);
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
