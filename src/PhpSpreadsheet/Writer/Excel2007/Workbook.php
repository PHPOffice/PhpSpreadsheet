<?php
namespace PHPExcel\Writer\Excel2007;

/**
 * PHPExcel_Writer_Excel2007_Workbook
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class Workbook extends WriterPart
{
    /**
     * Write workbook to XML format
     *
     * @param \PHPExcel\SpreadSheet $pPHPExcel
     * @param    boolean        $recalcRequired    Indicate whether formulas should be recalculated before writing
     * @return string  XML Output
     * @throws     \PHPExcel\Writer\Exception
     */
    public function writeWorkbook(\PHPExcel\SpreadSheet $pPHPExcel = null, $recalcRequired = false)
    {
        // Create XML writer
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new \PHPExcel\Shared\XMLWriter(\PHPExcel\Shared\XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new \PHPExcel\Shared\XMLWriter(\PHPExcel\Shared\XMLWriter::STORAGE_MEMORY);
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
        $this->writeWorkbookProtection($objWriter, $pPHPExcel);

        // bookViews
        if ($this->getParentWriter()->getOffice2003Compatibility() === false) {
            $this->writeBookViews($objWriter, $pPHPExcel);
        }

        // sheets
        $this->writeSheets($objWriter, $pPHPExcel);

        // definedNames
        $this->writeDefinedNames($objWriter, $pPHPExcel);

        // calcPr
        $this->writeCalcPr($objWriter, $recalcRequired);

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write file version
     *
     * @param     \PHPExcel\Shared\XMLWriter $objWriter         XML Writer
     * @throws     \PHPExcel\Writer\Exception
     */
    private function writeFileVersion(\PHPExcel\Shared\XMLWriter $objWriter)
    {
        $objWriter->startElement('fileVersion');
        $objWriter->writeAttribute('appName', 'xl');
        $objWriter->writeAttribute('lastEdited', '4');
        $objWriter->writeAttribute('lowestEdited', '4');
        $objWriter->writeAttribute('rupBuild', '4505');
        $objWriter->endElement();
    }

    /**
     * Write WorkbookPr
     *
     * @param     \PHPExcel\Shared\XMLWriter $objWriter         XML Writer
     * @throws     \PHPExcel\Writer\Exception
     */
    private function writeWorkbookPr(\PHPExcel\Shared\XMLWriter $objWriter)
    {
        $objWriter->startElement('workbookPr');

        if (\PHPExcel\Shared\Date::getExcelCalendar() == \PHPExcel\Shared\Date::CALENDAR_MAC_1904) {
            $objWriter->writeAttribute('date1904', '1');
        }

        $objWriter->writeAttribute('codeName', 'ThisWorkbook');

        $objWriter->endElement();
    }

    /**
     * Write BookViews
     *
     * @param     \PHPExcel\Shared\XMLWriter     $objWriter         XML Writer
     * @param     PHPExcel                    $pPHPExcel
     * @throws     \PHPExcel\Writer\Exception
     */
    private function writeBookViews(\PHPExcel\Shared\XMLWriter $objWriter, \PHPExcel\SpreadSheet $pPHPExcel = null)
    {
        // bookViews
        $objWriter->startElement('bookViews');

        // workbookView
        $objWriter->startElement('workbookView');

        $objWriter->writeAttribute('activeTab', $pPHPExcel->getActiveSheetIndex());
        $objWriter->writeAttribute('autoFilterDateGrouping', '1');
        $objWriter->writeAttribute('firstSheet', '0');
        $objWriter->writeAttribute('minimized', '0');
        $objWriter->writeAttribute('showHorizontalScroll', '1');
        $objWriter->writeAttribute('showSheetTabs', '1');
        $objWriter->writeAttribute('showVerticalScroll', '1');
        $objWriter->writeAttribute('tabRatio', '600');
        $objWriter->writeAttribute('visibility', 'visible');

        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write WorkbookProtection
     *
     * @param     \PHPExcel\Shared\XMLWriter     $objWriter         XML Writer
     * @param     PHPExcel                    $pPHPExcel
     * @throws     \PHPExcel\Writer\Exception
     */
    private function writeWorkbookProtection(\PHPExcel\Shared\XMLWriter $objWriter, \PHPExcel\SpreadSheet $pPHPExcel = null)
    {
        if ($pPHPExcel->getSecurity()->isSecurityEnabled()) {
            $objWriter->startElement('workbookProtection');
            $objWriter->writeAttribute('lockRevision', ($pPHPExcel->getSecurity()->getLockRevision() ? 'true' : 'false'));
            $objWriter->writeAttribute('lockStructure', ($pPHPExcel->getSecurity()->getLockStructure() ? 'true' : 'false'));
            $objWriter->writeAttribute('lockWindows', ($pPHPExcel->getSecurity()->getLockWindows() ? 'true' : 'false'));

            if ($pPHPExcel->getSecurity()->getRevisionsPassword() != '') {
                $objWriter->writeAttribute('revisionsPassword', $pPHPExcel->getSecurity()->getRevisionsPassword());
            }

            if ($pPHPExcel->getSecurity()->getWorkbookPassword() != '') {
                $objWriter->writeAttribute('workbookPassword', $pPHPExcel->getSecurity()->getWorkbookPassword());
            }

            $objWriter->endElement();
        }
    }

    /**
     * Write calcPr
     *
     * @param     \PHPExcel\Shared\XMLWriter    $objWriter        XML Writer
     * @param    boolean                        $recalcRequired    Indicate whether formulas should be recalculated before writing
     * @throws     \PHPExcel\Writer\Exception
     */
    private function writeCalcPr(\PHPExcel\Shared\XMLWriter $objWriter, $recalcRequired = true)
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

        $objWriter->endElement();
    }

    /**
     * Write sheets
     *
     * @param     \PHPExcel\Shared\XMLWriter     $objWriter         XML Writer
     * @param     PHPExcel                    $pPHPExcel
     * @throws     \PHPExcel\Writer\Exception
     */
    private function writeSheets(\PHPExcel\Shared\XMLWriter $objWriter, \PHPExcel\SpreadSheet $pPHPExcel = null)
    {
        // Write sheets
        $objWriter->startElement('sheets');
        $sheetCount = $pPHPExcel->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            // sheet
            $this->writeSheet(
                $objWriter,
                $pPHPExcel->getSheet($i)->getTitle(),
                ($i + 1),
                ($i + 1 + 3),
                $pPHPExcel->getSheet($i)->getSheetState()
            );
        }

        $objWriter->endElement();
    }

    /**
     * Write sheet
     *
     * @param     \PHPExcel\Shared\XMLWriter     $objWriter         XML Writer
     * @param     string                         $pSheetname         Sheet name
     * @param     int                            $pSheetId             Sheet id
     * @param     int                            $pRelId                Relationship ID
     * @param   string                      $sheetState         Sheet state (visible, hidden, veryHidden)
     * @throws     \PHPExcel\Writer\Exception
     */
    private function writeSheet(\PHPExcel\Shared\XMLWriter $objWriter, $pSheetname = '', $pSheetId = 1, $pRelId = 1, $sheetState = 'visible')
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
            throw new \PHPExcel\Writer\Exception("Invalid parameters passed.");
        }
    }

    /**
     * Write Defined Names
     *
     * @param     \PHPExcel\Shared\XMLWriter    $objWriter         XML Writer
     * @param     PHPExcel                    $pPHPExcel
     * @throws     \PHPExcel\Writer\Exception
     */
    private function writeDefinedNames(\PHPExcel\Shared\XMLWriter $objWriter, \PHPExcel\SpreadSheet $pPHPExcel = null)
    {
        // Write defined names
        $objWriter->startElement('definedNames');

        // Named ranges
        if (count($pPHPExcel->getNamedRanges()) > 0) {
            // Named ranges
            $this->writeNamedRanges($objWriter, $pPHPExcel);
        }

        // Other defined names
        $sheetCount = $pPHPExcel->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            // definedName for autoFilter
            $this->writeDefinedNameForAutofilter($objWriter, $pPHPExcel->getSheet($i), $i);

            // definedName for Print_Titles
            $this->writeDefinedNameForPrintTitles($objWriter, $pPHPExcel->getSheet($i), $i);

            // definedName for Print_Area
            $this->writeDefinedNameForPrintArea($objWriter, $pPHPExcel->getSheet($i), $i);
        }

        $objWriter->endElement();
    }

    /**
     * Write named ranges
     *
     * @param     \PHPExcel\Shared\XMLWriter    $objWriter         XML Writer
     * @param \PHPExcel\SpreadSheet $pPHPExcel
     * @throws     \PHPExcel\Writer\Exception
     */
    private function writeNamedRanges(\PHPExcel\Shared\XMLWriter $objWriter, \PHPExcel\SpreadSheet $pPHPExcel)
    {
        // Loop named ranges
        $namedRanges = $pPHPExcel->getNamedRanges();
        foreach ($namedRanges as $namedRange) {
            $this->writeDefinedNameForNamedRange($objWriter, $namedRange);
        }
    }

    /**
     * Write Defined Name for named range
     *
     * @param     \PHPExcel\Shared\XMLWriter    $objWriter         XML Writer
     * @param     \PHPExcel\NamedRange            $pNamedRange
     * @throws     \PHPExcel\Writer\Exception
     */
    private function writeDefinedNameForNamedRange(\PHPExcel\Shared\XMLWriter $objWriter, \PHPExcel\NamedRange $pNamedRange)
    {
        // definedName for named range
        $objWriter->startElement('definedName');
        $objWriter->writeAttribute('name', $pNamedRange->getName());
        if ($pNamedRange->getLocalOnly()) {
            $objWriter->writeAttribute('localSheetId', $pNamedRange->getScope()->getParent()->getIndex($pNamedRange->getScope()));
        }

        // Create absolute coordinate and write as raw text
        $range = \PHPExcel\Cell::splitRange($pNamedRange->getRange());
        for ($i = 0; $i < count($range); $i++) {
            $range[$i][0] = '\'' . str_replace("'", "''", $pNamedRange->getWorksheet()->getTitle()) . '\'!' . \PHPExcel\Cell::absoluteReference($range[$i][0]);
            if (isset($range[$i][1])) {
                $range[$i][1] = \PHPExcel\Cell::absoluteReference($range[$i][1]);
            }
        }
        $range = \PHPExcel\Cell::buildRange($range);

        $objWriter->writeRawData($range);

        $objWriter->endElement();
    }

    /**
     * Write Defined Name for autoFilter
     *
     * @param     \PHPExcel\Shared\XMLWriter    $objWriter         XML Writer
     * @param     \PHPExcel\Worksheet            $pSheet
     * @param     int                            $pSheetId
     * @throws     \PHPExcel\Writer\Exception
     */
    private function writeDefinedNameForAutofilter(\PHPExcel\Shared\XMLWriter $objWriter, \PHPExcel\Worksheet $pSheet = null, $pSheetId = 0)
    {
        // definedName for autoFilter
        $autoFilterRange = $pSheet->getAutoFilter()->getRange();
        if (!empty($autoFilterRange)) {
            $objWriter->startElement('definedName');
            $objWriter->writeAttribute('name', '_xlnm._FilterDatabase');
            $objWriter->writeAttribute('localSheetId', $pSheetId);
            $objWriter->writeAttribute('hidden', '1');

            // Create absolute coordinate and write as raw text
            $range = \PHPExcel\Cell::splitRange($autoFilterRange);
            $range = $range[0];
            //    Strip any worksheet ref so we can make the cell ref absolute
            if (strpos($range[0], '!') !== false) {
                list($ws, $range[0]) = explode('!', $range[0]);
            }

            $range[0] = \PHPExcel\Cell::absoluteCoordinate($range[0]);
            $range[1] = \PHPExcel\Cell::absoluteCoordinate($range[1]);
            $range = implode(':', $range);

            $objWriter->writeRawData('\'' . str_replace("'", "''", $pSheet->getTitle()) . '\'!' . $range);

            $objWriter->endElement();
        }
    }

    /**
     * Write Defined Name for PrintTitles
     *
     * @param     \PHPExcel\Shared\XMLWriter    $objWriter         XML Writer
     * @param     \PHPExcel\Worksheet            $pSheet
     * @param     int                            $pSheetId
     * @throws     \PHPExcel\Writer\Exception
     */
    private function writeDefinedNameForPrintTitles(\PHPExcel\Shared\XMLWriter $objWriter, \PHPExcel\Worksheet $pSheet = null, $pSheetId = 0)
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
     * Write Defined Name for PrintTitles
     *
     * @param     \PHPExcel\Shared\XMLWriter    $objWriter         XML Writer
     * @param     \PHPExcel\Worksheet            $pSheet
     * @param     int                            $pSheetId
     * @throws     \PHPExcel\Writer\Exception
     */
    private function writeDefinedNameForPrintArea(\PHPExcel\Shared\XMLWriter $objWriter, \PHPExcel\Worksheet $pSheet = null, $pSheetId = 0)
    {
        // definedName for PrintArea
        if ($pSheet->getPageSetup()->isPrintAreaSet()) {
            $objWriter->startElement('definedName');
            $objWriter->writeAttribute('name', '_xlnm.Print_Area');
            $objWriter->writeAttribute('localSheetId', $pSheetId);

            // Setting string
            $settingString = '';

            // Print area
            $printArea = \PHPExcel\Cell::splitRange($pSheet->getPageSetup()->getPrintArea());

            $chunks = array();
            foreach ($printArea as $printAreaRect) {
                $printAreaRect[0] = \PHPExcel\Cell::absoluteReference($printAreaRect[0]);
                $printAreaRect[1] = \PHPExcel\Cell::absoluteReference($printAreaRect[1]);
                $chunks[] = '\'' . str_replace("'", "''", $pSheet->getTitle()) . '\'!' . implode(':', $printAreaRect);
            }

            $objWriter->writeRawData(implode(',', $chunks));

            $objWriter->endElement();
        }
    }
}
