<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class Workbook extends WriterPart
{
    /**
     * Write workbook to XML format.
     *
     * @param Spreadsheet $spreadsheet
     * @param bool $recalcRequired Indicate whether formulas should be recalculated before writing
     *
     * @throws WriterException
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
        $this->writeDefinedNames($objWriter, $spreadsheet);

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
    private function writeFileVersion(XMLWriter $objWriter)
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
    private function writeWorkbookPr(XMLWriter $objWriter)
    {
        $objWriter->startElement('workbookPr');

        if (Date::getExcelCalendar() == Date::CALENDAR_MAC_1904) {
            $objWriter->writeAttribute('date1904', '1');
        }

        $objWriter->writeAttribute('codeName', 'ThisWorkbook');

        $objWriter->endElement();
    }

    /**
     * Write BookViews.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param Spreadsheet $spreadsheet
     */
    private function writeBookViews(XMLWriter $objWriter, Spreadsheet $spreadsheet)
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
     * @param Spreadsheet $spreadsheet
     */
    private function writeWorkbookProtection(XMLWriter $objWriter, Spreadsheet $spreadsheet)
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
    private function writeCalcPr(XMLWriter $objWriter, $recalcRequired = true)
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
     * @param Spreadsheet $spreadsheet
     *
     * @throws WriterException
     */
    private function writeSheets(XMLWriter $objWriter, Spreadsheet $spreadsheet)
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
     * @param string $pSheetname Sheet name
     * @param int $pSheetId Sheet id
     * @param int $pRelId Relationship ID
     * @param string $sheetState Sheet state (visible, hidden, veryHidden)
     *
     * @throws WriterException
     */
    private function writeSheet(XMLWriter $objWriter, $pSheetname, $pSheetId = 1, $pRelId = 1, $sheetState = 'visible')
    {
        if ($pSheetname != '') {
            // Write sheet
            $objWriter->startElement('sheet');
            $objWriter->writeAttribute('name', $pSheetname);
            $objWriter->writeAttribute('sheetId', $pSheetId);
            if ($sheetState != 'visible' && $sheetState != '') {
                $objWriter->writeAttribute('state', $sheetState);
            }
            $objWriter->writeAttribute('r:id', 'rId' . $pRelId);
            $objWriter->endElement();
        } else {
            throw new WriterException('Invalid parameters passed.');
        }
    }

    /**
     * Write Defined Names.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param Spreadsheet $spreadsheet
     *
     * @throws WriterException
     */
    private function writeDefinedNames(XMLWriter $objWriter, Spreadsheet $spreadsheet)
    {
        // Write defined names
        $objWriter->startElement('definedNames');

        // Named ranges
        if (count($spreadsheet->getNamedRanges()) > 0) {
            // Named ranges
            $this->writeNamedRanges($objWriter, $spreadsheet);
        }

        // Other defined names
        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            // definedName for autoFilter
            $this->writeDefinedNameForAutofilter($objWriter, $spreadsheet->getSheet($i), $i);

            // definedName for Print_Titles
            $this->writeDefinedNameForPrintTitles($objWriter, $spreadsheet->getSheet($i), $i);

            // definedName for Print_Area
            $this->writeDefinedNameForPrintArea($objWriter, $spreadsheet->getSheet($i), $i);
        }

        $objWriter->endElement();
    }

    /**
     * Write named ranges.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param Spreadsheet $spreadsheet
     *
     * @throws WriterException
     */
    private function writeNamedRanges(XMLWriter $objWriter, Spreadsheet $spreadsheet)
    {
        // Loop named ranges
        $namedRanges = $spreadsheet->getNamedRanges();
        foreach ($namedRanges as $namedRange) {
            $this->writeDefinedNameForNamedRange($objWriter, $namedRange);
        }
    }

    /**
     * Write Defined Name for named range.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param NamedRange $pNamedRange
     */
    private function writeDefinedNameForNamedRange(XMLWriter $objWriter, NamedRange $pNamedRange)
    {
        // definedName for named range
        $objWriter->startElement('definedName');
        $objWriter->writeAttribute('name', $pNamedRange->getName());
        if ($pNamedRange->getLocalOnly()) {
            $objWriter->writeAttribute('localSheetId', $pNamedRange->getScope()->getParent()->getIndex($pNamedRange->getScope()));
        }

        // Create absolute coordinate and write as raw text
        $range = Coordinate::splitRange($pNamedRange->getRange());
        $iMax = count($range);
        for ($i = 0; $i < $iMax; ++$i) {
            $range[$i][0] = '\'' . str_replace("'", "''", $pNamedRange->getWorksheet()->getTitle()) . '\'!' . Coordinate::absoluteReference($range[$i][0]);
            if (isset($range[$i][1])) {
                $range[$i][1] = Coordinate::absoluteReference($range[$i][1]);
            }
        }
        $range = Coordinate::buildRange($range);

        $objWriter->writeRawData($range);

        $objWriter->endElement();
    }

    /**
     * Write Defined Name for autoFilter.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param Worksheet $pSheet
     * @param int $pSheetId
     */
    private function writeDefinedNameForAutofilter(XMLWriter $objWriter, Worksheet $pSheet, $pSheetId = 0)
    {
        // definedName for autoFilter
        $autoFilterRange = $pSheet->getAutoFilter()->getRange();
        if (!empty($autoFilterRange)) {
            $objWriter->startElement('definedName');
            $objWriter->writeAttribute('name', '_xlnm._FilterDatabase');
            $objWriter->writeAttribute('localSheetId', $pSheetId);
            $objWriter->writeAttribute('hidden', '1');

            // Create absolute coordinate and write as raw text
            $range = Coordinate::splitRange($autoFilterRange);
            $range = $range[0];
            //    Strip any worksheet ref so we can make the cell ref absolute
            list($ws, $range[0]) = Worksheet::extractSheetTitle($range[0], true);

            $range[0] = Coordinate::absoluteCoordinate($range[0]);
            $range[1] = Coordinate::absoluteCoordinate($range[1]);
            $range = implode(':', $range);

            $objWriter->writeRawData('\'' . str_replace("'", "''", $pSheet->getTitle()) . '\'!' . $range);

            $objWriter->endElement();
        }
    }

    /**
     * Write Defined Name for PrintTitles.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param Worksheet $pSheet
     * @param int $pSheetId
     */
    private function writeDefinedNameForPrintTitles(XMLWriter $objWriter, Worksheet $pSheet, $pSheetId = 0)
    {
        // definedName for PrintTitles
        if ($pSheet->getPageSetup()->isColumnsToRepeatAtLeftSet() || $pSheet->getPageSetup()->isRowsToRepeatAtTopSet()) {
            $objWriter->startElement('definedName');
            $objWriter->writeAttribute('name', '_xlnm.Print_Titles');
            $objWriter->writeAttribute('localSheetId', $pSheetId);

            // Setting string
            $settingString = '';

            // Columns to repeat
            if ($pSheet->getPageSetup()->isColumnsToRepeatAtLeftSet()) {
                $repeat = $pSheet->getPageSetup()->getColumnsToRepeatAtLeft();

                $settingString .= '\'' . str_replace("'", "''", $pSheet->getTitle()) . '\'!$' . $repeat[0] . ':$' . $repeat[1];
            }

            // Rows to repeat
            if ($pSheet->getPageSetup()->isRowsToRepeatAtTopSet()) {
                if ($pSheet->getPageSetup()->isColumnsToRepeatAtLeftSet()) {
                    $settingString .= ',';
                }

                $repeat = $pSheet->getPageSetup()->getRowsToRepeatAtTop();

                $settingString .= '\'' . str_replace("'", "''", $pSheet->getTitle()) . '\'!$' . $repeat[0] . ':$' . $repeat[1];
            }

            $objWriter->writeRawData($settingString);

            $objWriter->endElement();
        }
    }

    /**
     * Write Defined Name for PrintTitles.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param Worksheet $pSheet
     * @param int $pSheetId
     */
    private function writeDefinedNameForPrintArea(XMLWriter $objWriter, Worksheet $pSheet, $pSheetId = 0)
    {
        // definedName for PrintArea
        if ($pSheet->getPageSetup()->isPrintAreaSet()) {
            $objWriter->startElement('definedName');
            $objWriter->writeAttribute('name', '_xlnm.Print_Area');
            $objWriter->writeAttribute('localSheetId', $pSheetId);

            // Print area
            $printArea = Coordinate::splitRange($pSheet->getPageSetup()->getPrintArea());

            $chunks = [];
            foreach ($printArea as $printAreaRect) {
                $printAreaRect[0] = Coordinate::absoluteReference($printAreaRect[0]);
                $printAreaRect[1] = Coordinate::absoluteReference($printAreaRect[1]);
                $chunks[] = '\'' . str_replace("'", "''", $pSheet->getTitle()) . '\'!' . implode(':', $printAreaRect);
            }

            $objWriter->writeRawData(implode(',', $chunks));

            $objWriter->endElement();
        }
    }
}
