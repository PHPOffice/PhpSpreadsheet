<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * PhpSpreadsheet.
 *
 * Copyright (c) 2006 - 2015 PhpSpreadsheet
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category   PhpSpreadsheet
 *
 * @copyright  Copyright (c) 2006 - 2015 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */

/**
 * @category   PhpSpreadsheet
 *
 * @copyright  Copyright (c) 2006 - 2015 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 */
class Worksheet extends WriterPart
{
    /**
     * Write worksheet to XML format.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet
     * @param string[] $pStringTable
     * @param bool $includeCharts Flag indicating if we should write charts
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return string XML Output
     */
    public function writeWorksheet($pSheet = null, $pStringTable = null, $includeCharts = false)
    {
        if (!is_null($pSheet)) {
            // Create XML writer
            $objWriter = null;
            if ($this->getParentWriter()->getUseDiskCaching()) {
                $objWriter = new \PhpOffice\PhpSpreadsheet\Shared\XMLWriter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
            } else {
                $objWriter = new \PhpOffice\PhpSpreadsheet\Shared\XMLWriter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter::STORAGE_MEMORY);
            }

            // XML header
            $objWriter->startDocument('1.0', 'UTF-8', 'yes');

            // Worksheet
            $objWriter->startElement('worksheet');
            $objWriter->writeAttribute('xml:space', 'preserve');
            $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $objWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

                // sheetPr
                $this->writeSheetPr($objWriter, $pSheet);

                // Dimension
                $this->writeDimension($objWriter, $pSheet);

                // sheetViews
                $this->writeSheetViews($objWriter, $pSheet);

                // sheetFormatPr
                $this->writeSheetFormatPr($objWriter, $pSheet);

                // cols
                $this->writeCols($objWriter, $pSheet);

                // sheetData
                $this->writeSheetData($objWriter, $pSheet, $pStringTable);

                // sheetProtection
                $this->writeSheetProtection($objWriter, $pSheet);

                // protectedRanges
                $this->writeProtectedRanges($objWriter, $pSheet);

                // autoFilter
                $this->writeAutoFilter($objWriter, $pSheet);

                // mergeCells
                $this->writeMergeCells($objWriter, $pSheet);

                // conditionalFormatting
                $this->writeConditionalFormatting($objWriter, $pSheet);

                // dataValidations
                $this->writeDataValidations($objWriter, $pSheet);

                // hyperlinks
                $this->writeHyperlinks($objWriter, $pSheet);

                // Print options
                $this->writePrintOptions($objWriter, $pSheet);

                // Page margins
                $this->writePageMargins($objWriter, $pSheet);

                // Page setup
                $this->writePageSetup($objWriter, $pSheet);

                // Header / footer
                $this->writeHeaderFooter($objWriter, $pSheet);

                // Breaks
                $this->writeBreaks($objWriter, $pSheet);

                // Drawings and/or Charts
                $this->writeDrawings($objWriter, $pSheet, $includeCharts);

                // LegacyDrawing
                $this->writeLegacyDrawing($objWriter, $pSheet);

                // LegacyDrawingHF
                $this->writeLegacyDrawingHF($objWriter, $pSheet);

            $objWriter->endElement();

            // Return
            return $objWriter->getData();
        }
        throw new \PhpOffice\PhpSpreadsheet\Writer\Exception('Invalid \\PhpOffice\\PhpSpreadsheet\\Worksheet object passed.');
    }

    /**
     * Write SheetPr.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeSheetPr(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // sheetPr
        $objWriter->startElement('sheetPr');
        if ($pSheet->getParent()->hasMacros()) {
            //if the workbook have macros, we need to have codeName for the sheet
            if ($pSheet->hasCodeName() == false) {
                $pSheet->setCodeName($pSheet->getTitle());
            }
            $objWriter->writeAttribute('codeName', $pSheet->getCodeName());
        }
        $autoFilterRange = $pSheet->getAutoFilter()->getRange();
        if (!empty($autoFilterRange)) {
            $objWriter->writeAttribute('filterMode', 1);
            $pSheet->getAutoFilter()->showHideRows();
        }

        // tabColor
        if ($pSheet->isTabColorSet()) {
            $objWriter->startElement('tabColor');
            $objWriter->writeAttribute('rgb', $pSheet->getTabColor()->getARGB());
            $objWriter->endElement();
        }

        // outlinePr
        $objWriter->startElement('outlinePr');
        $objWriter->writeAttribute('summaryBelow', ($pSheet->getShowSummaryBelow() ? '1' : '0'));
        $objWriter->writeAttribute('summaryRight', ($pSheet->getShowSummaryRight() ? '1' : '0'));
        $objWriter->endElement();

        // pageSetUpPr
        if ($pSheet->getPageSetup()->getFitToPage()) {
            $objWriter->startElement('pageSetUpPr');
            $objWriter->writeAttribute('fitToPage', '1');
            $objWriter->endElement();
        }

        $objWriter->endElement();
    }

    /**
     * Write Dimension.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeDimension(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // dimension
        $objWriter->startElement('dimension');
        $objWriter->writeAttribute('ref', $pSheet->calculateWorksheetDimension());
        $objWriter->endElement();
    }

    /**
     * Write SheetViews.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeSheetViews(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // sheetViews
        $objWriter->startElement('sheetViews');

        // Sheet selected?
        $sheetSelected = false;
        if ($this->getParentWriter()->getSpreadsheet()->getIndex($pSheet) == $this->getParentWriter()->getSpreadsheet()->getActiveSheetIndex()) {
            $sheetSelected = true;
        }

        // sheetView
        $objWriter->startElement('sheetView');
        $objWriter->writeAttribute('tabSelected', $sheetSelected ? '1' : '0');
        $objWriter->writeAttribute('workbookViewId', '0');

        // Zoom scales
        if ($pSheet->getSheetView()->getZoomScale() != 100) {
            $objWriter->writeAttribute('zoomScale', $pSheet->getSheetView()->getZoomScale());
        }
        if ($pSheet->getSheetView()->getZoomScaleNormal() != 100) {
            $objWriter->writeAttribute('zoomScaleNormal', $pSheet->getSheetView()->getZoomScaleNormal());
        }

        // View Layout Type
        if ($pSheet->getSheetView()->getView() !== \PhpOffice\PhpSpreadsheet\Worksheet\SheetView::SHEETVIEW_NORMAL) {
            $objWriter->writeAttribute('view', $pSheet->getSheetView()->getView());
        }

        // Gridlines
        if ($pSheet->getShowGridlines()) {
            $objWriter->writeAttribute('showGridLines', 'true');
        } else {
            $objWriter->writeAttribute('showGridLines', 'false');
        }

        // Row and column headers
        if ($pSheet->getShowRowColHeaders()) {
            $objWriter->writeAttribute('showRowColHeaders', '1');
        } else {
            $objWriter->writeAttribute('showRowColHeaders', '0');
        }

        // Right-to-left
        if ($pSheet->getRightToLeft()) {
            $objWriter->writeAttribute('rightToLeft', 'true');
        }

        $activeCell = $pSheet->getActiveCell();

        // Pane
        $pane = '';
        $topLeftCell = $pSheet->getFreezePane();
        if (($topLeftCell != '') && ($topLeftCell != 'A1')) {
            $activeCell = $topLeftCell;
            // Calculate freeze coordinates
            $xSplit = $ySplit = 0;

            list($xSplit, $ySplit) = \PhpOffice\PhpSpreadsheet\Cell::coordinateFromString($topLeftCell);
            $xSplit = \PhpOffice\PhpSpreadsheet\Cell::columnIndexFromString($xSplit);

            // pane
            $pane = 'topRight';
            $objWriter->startElement('pane');
            if ($xSplit > 1) {
                $objWriter->writeAttribute('xSplit', $xSplit - 1);
            }
            if ($ySplit > 1) {
                $objWriter->writeAttribute('ySplit', $ySplit - 1);
                $pane = ($xSplit > 1) ? 'bottomRight' : 'bottomLeft';
            }
            $objWriter->writeAttribute('topLeftCell', $topLeftCell);
            $objWriter->writeAttribute('activePane', $pane);
            $objWriter->writeAttribute('state', 'frozen');
            $objWriter->endElement();

            if (($xSplit > 1) && ($ySplit > 1)) {
                //    Write additional selections if more than two panes (ie both an X and a Y split)
                $objWriter->startElement('selection');
                $objWriter->writeAttribute('pane', 'topRight');
                $objWriter->endElement();
                $objWriter->startElement('selection');
                $objWriter->writeAttribute('pane', 'bottomLeft');
                $objWriter->endElement();
            }
        }

        // Selection
//      if ($pane != '') {
        // Only need to write selection element if we have a split pane
        // We cheat a little by over-riding the active cell selection, setting it to the split cell
        $objWriter->startElement('selection');
        if ($pane != '') {
            $objWriter->writeAttribute('pane', $pane);
        }
        $objWriter->writeAttribute('activeCell', $activeCell);
        $objWriter->writeAttribute('sqref', $activeCell);
        $objWriter->endElement();
//      }

        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write SheetFormatPr.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeSheetFormatPr(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // sheetFormatPr
        $objWriter->startElement('sheetFormatPr');

        // Default row height
        if ($pSheet->getDefaultRowDimension()->getRowHeight() >= 0) {
            $objWriter->writeAttribute('customHeight', 'true');
            $objWriter->writeAttribute('defaultRowHeight', \PhpOffice\PhpSpreadsheet\Shared\StringHelper::formatNumber($pSheet->getDefaultRowDimension()->getRowHeight()));
        } else {
            $objWriter->writeAttribute('defaultRowHeight', '14.4');
        }

        // Set Zero Height row
        if ((string) $pSheet->getDefaultRowDimension()->getZeroHeight() == '1' ||
            strtolower((string) $pSheet->getDefaultRowDimension()->getZeroHeight()) == 'true') {
            $objWriter->writeAttribute('zeroHeight', '1');
        }

        // Default column width
        if ($pSheet->getDefaultColumnDimension()->getWidth() >= 0) {
            $objWriter->writeAttribute('defaultColWidth', \PhpOffice\PhpSpreadsheet\Shared\StringHelper::formatNumber($pSheet->getDefaultColumnDimension()->getWidth()));
        }

        // Outline level - row
        $outlineLevelRow = 0;
        foreach ($pSheet->getRowDimensions() as $dimension) {
            if ($dimension->getOutlineLevel() > $outlineLevelRow) {
                $outlineLevelRow = $dimension->getOutlineLevel();
            }
        }
        $objWriter->writeAttribute('outlineLevelRow', (int) $outlineLevelRow);

        // Outline level - column
        $outlineLevelCol = 0;
        foreach ($pSheet->getColumnDimensions() as $dimension) {
            if ($dimension->getOutlineLevel() > $outlineLevelCol) {
                $outlineLevelCol = $dimension->getOutlineLevel();
            }
        }
        $objWriter->writeAttribute('outlineLevelCol', (int) $outlineLevelCol);

        $objWriter->endElement();
    }

    /**
     * Write Cols.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeCols(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // cols
        if (count($pSheet->getColumnDimensions()) > 0) {
            $objWriter->startElement('cols');

            $pSheet->calculateColumnWidths();

            // Loop through column dimensions
            foreach ($pSheet->getColumnDimensions() as $colDimension) {
                // col
                $objWriter->startElement('col');
                $objWriter->writeAttribute('min', \PhpOffice\PhpSpreadsheet\Cell::columnIndexFromString($colDimension->getColumnIndex()));
                $objWriter->writeAttribute('max', \PhpOffice\PhpSpreadsheet\Cell::columnIndexFromString($colDimension->getColumnIndex()));

                if ($colDimension->getWidth() < 0) {
                    // No width set, apply default of 10
                    $objWriter->writeAttribute('width', '9.10');
                } else {
                    // Width set
                    $objWriter->writeAttribute('width', \PhpOffice\PhpSpreadsheet\Shared\StringHelper::formatNumber($colDimension->getWidth()));
                }

                // Column visibility
                if ($colDimension->getVisible() == false) {
                    $objWriter->writeAttribute('hidden', 'true');
                }

                // Auto size?
                if ($colDimension->getAutoSize()) {
                    $objWriter->writeAttribute('bestFit', 'true');
                }

                // Custom width?
                if ($colDimension->getWidth() != $pSheet->getDefaultColumnDimension()->getWidth()) {
                    $objWriter->writeAttribute('customWidth', 'true');
                }

                // Collapsed
                if ($colDimension->getCollapsed() == true) {
                    $objWriter->writeAttribute('collapsed', 'true');
                }

                // Outline level
                if ($colDimension->getOutlineLevel() > 0) {
                    $objWriter->writeAttribute('outlineLevel', $colDimension->getOutlineLevel());
                }

                // Style
                $objWriter->writeAttribute('style', $colDimension->getXfIndex());

                $objWriter->endElement();
            }

            $objWriter->endElement();
        }
    }

    /**
     * Write SheetProtection.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeSheetProtection(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // sheetProtection
        $objWriter->startElement('sheetProtection');

        if ($pSheet->getProtection()->getPassword() != '') {
            $objWriter->writeAttribute('password', $pSheet->getProtection()->getPassword());
        }

        $objWriter->writeAttribute('sheet', ($pSheet->getProtection()->getSheet() ? 'true' : 'false'));
        $objWriter->writeAttribute('objects', ($pSheet->getProtection()->getObjects() ? 'true' : 'false'));
        $objWriter->writeAttribute('scenarios', ($pSheet->getProtection()->getScenarios() ? 'true' : 'false'));
        $objWriter->writeAttribute('formatCells', ($pSheet->getProtection()->getFormatCells() ? 'true' : 'false'));
        $objWriter->writeAttribute('formatColumns', ($pSheet->getProtection()->getFormatColumns() ? 'true' : 'false'));
        $objWriter->writeAttribute('formatRows', ($pSheet->getProtection()->getFormatRows() ? 'true' : 'false'));
        $objWriter->writeAttribute('insertColumns', ($pSheet->getProtection()->getInsertColumns() ? 'true' : 'false'));
        $objWriter->writeAttribute('insertRows', ($pSheet->getProtection()->getInsertRows() ? 'true' : 'false'));
        $objWriter->writeAttribute('insertHyperlinks', ($pSheet->getProtection()->getInsertHyperlinks() ? 'true' : 'false'));
        $objWriter->writeAttribute('deleteColumns', ($pSheet->getProtection()->getDeleteColumns() ? 'true' : 'false'));
        $objWriter->writeAttribute('deleteRows', ($pSheet->getProtection()->getDeleteRows() ? 'true' : 'false'));
        $objWriter->writeAttribute('selectLockedCells', ($pSheet->getProtection()->getSelectLockedCells() ? 'true' : 'false'));
        $objWriter->writeAttribute('sort', ($pSheet->getProtection()->getSort() ? 'true' : 'false'));
        $objWriter->writeAttribute('autoFilter', ($pSheet->getProtection()->getAutoFilter() ? 'true' : 'false'));
        $objWriter->writeAttribute('pivotTables', ($pSheet->getProtection()->getPivotTables() ? 'true' : 'false'));
        $objWriter->writeAttribute('selectUnlockedCells', ($pSheet->getProtection()->getSelectUnlockedCells() ? 'true' : 'false'));
        $objWriter->endElement();
    }

    /**
     * Write ConditionalFormatting.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeConditionalFormatting(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // Conditional id
        $id = 1;

        // Loop through styles in the current worksheet
        foreach ($pSheet->getConditionalStylesCollection() as $cellCoordinate => $conditionalStyles) {
            foreach ($conditionalStyles as $conditional) {
                // WHY was this again?
                // if ($this->getParentWriter()->getStylesConditionalHashTable()->getIndexForHashCode($conditional->getHashCode()) == '') {
                //    continue;
                // }
                if ($conditional->getConditionType() != \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_NONE) {
                    // conditionalFormatting
                    $objWriter->startElement('conditionalFormatting');
                    $objWriter->writeAttribute('sqref', $cellCoordinate);

                    // cfRule
                    $objWriter->startElement('cfRule');
                    $objWriter->writeAttribute('type', $conditional->getConditionType());
                    $objWriter->writeAttribute('dxfId', $this->getParentWriter()->getStylesConditionalHashTable()->getIndexForHashCode($conditional->getHashCode()));
                    $objWriter->writeAttribute('priority', $id++);

                    if (($conditional->getConditionType() == \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS || $conditional->getConditionType() == \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT)
                        && $conditional->getOperatorType() != \PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_NONE) {
                        $objWriter->writeAttribute('operator', $conditional->getOperatorType());
                    }

                    if ($conditional->getConditionType() == \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT
                        && !is_null($conditional->getText())) {
                        $objWriter->writeAttribute('text', $conditional->getText());
                    }

                    if ($conditional->getConditionType() == \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT
                        && $conditional->getOperatorType() == \PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT
                        && !is_null($conditional->getText())) {
                        $objWriter->writeElement('formula', 'NOT(ISERROR(SEARCH("' . $conditional->getText() . '",' . $cellCoordinate . ')))');
                    } elseif ($conditional->getConditionType() == \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT
                        && $conditional->getOperatorType() == \PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_BEGINSWITH
                        && !is_null($conditional->getText())) {
                        $objWriter->writeElement('formula', 'LEFT(' . $cellCoordinate . ',' . strlen($conditional->getText()) . ')="' . $conditional->getText() . '"');
                    } elseif ($conditional->getConditionType() == \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT
                        && $conditional->getOperatorType() == \PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_ENDSWITH
                        && !is_null($conditional->getText())) {
                        $objWriter->writeElement('formula', 'RIGHT(' . $cellCoordinate . ',' . strlen($conditional->getText()) . ')="' . $conditional->getText() . '"');
                    } elseif ($conditional->getConditionType() == \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT
                        && $conditional->getOperatorType() == \PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_NOTCONTAINS
                        && !is_null($conditional->getText())) {
                        $objWriter->writeElement('formula', 'ISERROR(SEARCH("' . $conditional->getText() . '",' . $cellCoordinate . '))');
                    } elseif ($conditional->getConditionType() == \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS
                        || $conditional->getConditionType() == \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT
                        || $conditional->getConditionType() == \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_EXPRESSION) {
                        foreach ($conditional->getConditions() as $formula) {
                            // Formula
                            $objWriter->writeElement('formula', $formula);
                        }
                    }

                    $objWriter->endElement();

                    $objWriter->endElement();
                }
            }
        }
    }

    /**
     * Write DataValidations.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeDataValidations(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // Datavalidation collection
        $dataValidationCollection = $pSheet->getDataValidationCollection();

        // Write data validations?
        if (!empty($dataValidationCollection)) {
            $objWriter->startElement('dataValidations');
            $objWriter->writeAttribute('count', count($dataValidationCollection));

            foreach ($dataValidationCollection as $coordinate => $dv) {
                $objWriter->startElement('dataValidation');

                if ($dv->getType() != '') {
                    $objWriter->writeAttribute('type', $dv->getType());
                }

                if ($dv->getErrorStyle() != '') {
                    $objWriter->writeAttribute('errorStyle', $dv->getErrorStyle());
                }

                if ($dv->getOperator() != '') {
                    $objWriter->writeAttribute('operator', $dv->getOperator());
                }

                $objWriter->writeAttribute('allowBlank', ($dv->getAllowBlank() ? '1' : '0'));
                $objWriter->writeAttribute('showDropDown', (!$dv->getShowDropDown() ? '1' : '0'));
                $objWriter->writeAttribute('showInputMessage', ($dv->getShowInputMessage() ? '1' : '0'));
                $objWriter->writeAttribute('showErrorMessage', ($dv->getShowErrorMessage() ? '1' : '0'));

                if ($dv->getErrorTitle() !== '') {
                    $objWriter->writeAttribute('errorTitle', $dv->getErrorTitle());
                }
                if ($dv->getError() !== '') {
                    $objWriter->writeAttribute('error', $dv->getError());
                }
                if ($dv->getPromptTitle() !== '') {
                    $objWriter->writeAttribute('promptTitle', $dv->getPromptTitle());
                }
                if ($dv->getPrompt() !== '') {
                    $objWriter->writeAttribute('prompt', $dv->getPrompt());
                }

                $objWriter->writeAttribute('sqref', $coordinate);

                if ($dv->getFormula1() !== '') {
                    $objWriter->writeElement('formula1', $dv->getFormula1());
                }
                if ($dv->getFormula2() !== '') {
                    $objWriter->writeElement('formula2', $dv->getFormula2());
                }

                $objWriter->endElement();
            }

            $objWriter->endElement();
        }
    }

    /**
     * Write Hyperlinks.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeHyperlinks(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // Hyperlink collection
        $hyperlinkCollection = $pSheet->getHyperlinkCollection();

        // Relation ID
        $relationId = 1;

        // Write hyperlinks?
        if (!empty($hyperlinkCollection)) {
            $objWriter->startElement('hyperlinks');

            foreach ($hyperlinkCollection as $coordinate => $hyperlink) {
                $objWriter->startElement('hyperlink');

                $objWriter->writeAttribute('ref', $coordinate);
                if (!$hyperlink->isInternal()) {
                    $objWriter->writeAttribute('r:id', 'rId_hyperlink_' . $relationId);
                    ++$relationId;
                } else {
                    $objWriter->writeAttribute('location', str_replace('sheet://', '', $hyperlink->getUrl()));
                }

                if ($hyperlink->getTooltip() != '') {
                    $objWriter->writeAttribute('tooltip', $hyperlink->getTooltip());
                }

                $objWriter->endElement();
            }

            $objWriter->endElement();
        }
    }

    /**
     * Write ProtectedRanges.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeProtectedRanges(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        if (count($pSheet->getProtectedCells()) > 0) {
            // protectedRanges
            $objWriter->startElement('protectedRanges');

            // Loop protectedRanges
            foreach ($pSheet->getProtectedCells() as $protectedCell => $passwordHash) {
                // protectedRange
                $objWriter->startElement('protectedRange');
                $objWriter->writeAttribute('name', 'p' . md5($protectedCell));
                $objWriter->writeAttribute('sqref', $protectedCell);
                if (!empty($passwordHash)) {
                    $objWriter->writeAttribute('password', $passwordHash);
                }
                $objWriter->endElement();
            }

            $objWriter->endElement();
        }
    }

    /**
     * Write MergeCells.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeMergeCells(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        if (count($pSheet->getMergeCells()) > 0) {
            // mergeCells
            $objWriter->startElement('mergeCells');

            // Loop mergeCells
            foreach ($pSheet->getMergeCells() as $mergeCell) {
                // mergeCell
                $objWriter->startElement('mergeCell');
                $objWriter->writeAttribute('ref', $mergeCell);
                $objWriter->endElement();
            }

            $objWriter->endElement();
        }
    }

    /**
     * Write PrintOptions.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writePrintOptions(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // printOptions
        $objWriter->startElement('printOptions');

        $objWriter->writeAttribute('gridLines', ($pSheet->getPrintGridlines() ? 'true' : 'false'));
        $objWriter->writeAttribute('gridLinesSet', 'true');

        if ($pSheet->getPageSetup()->getHorizontalCentered()) {
            $objWriter->writeAttribute('horizontalCentered', 'true');
        }

        if ($pSheet->getPageSetup()->getVerticalCentered()) {
            $objWriter->writeAttribute('verticalCentered', 'true');
        }

        $objWriter->endElement();
    }

    /**
     * Write PageMargins.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writePageMargins(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // pageMargins
        $objWriter->startElement('pageMargins');
        $objWriter->writeAttribute('left', \PhpOffice\PhpSpreadsheet\Shared\StringHelper::formatNumber($pSheet->getPageMargins()->getLeft()));
        $objWriter->writeAttribute('right', \PhpOffice\PhpSpreadsheet\Shared\StringHelper::formatNumber($pSheet->getPageMargins()->getRight()));
        $objWriter->writeAttribute('top', \PhpOffice\PhpSpreadsheet\Shared\StringHelper::formatNumber($pSheet->getPageMargins()->getTop()));
        $objWriter->writeAttribute('bottom', \PhpOffice\PhpSpreadsheet\Shared\StringHelper::formatNumber($pSheet->getPageMargins()->getBottom()));
        $objWriter->writeAttribute('header', \PhpOffice\PhpSpreadsheet\Shared\StringHelper::formatNumber($pSheet->getPageMargins()->getHeader()));
        $objWriter->writeAttribute('footer', \PhpOffice\PhpSpreadsheet\Shared\StringHelper::formatNumber($pSheet->getPageMargins()->getFooter()));
        $objWriter->endElement();
    }

    /**
     * Write AutoFilter.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeAutoFilter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        $autoFilterRange = $pSheet->getAutoFilter()->getRange();
        if (!empty($autoFilterRange)) {
            // autoFilter
            $objWriter->startElement('autoFilter');

            // Strip any worksheet reference from the filter coordinates
            $range = \PhpOffice\PhpSpreadsheet\Cell::splitRange($autoFilterRange);
            $range = $range[0];
            //    Strip any worksheet ref
            if (strpos($range[0], '!') !== false) {
                list($ws, $range[0]) = explode('!', $range[0]);
            }
            $range = implode(':', $range);

            $objWriter->writeAttribute('ref', str_replace('$', '', $range));

            $columns = $pSheet->getAutoFilter()->getColumns();
            if (count($columns > 0)) {
                foreach ($columns as $columnID => $column) {
                    $rules = $column->getRules();
                    if (count($rules) > 0) {
                        $objWriter->startElement('filterColumn');
                        $objWriter->writeAttribute('colId', $pSheet->getAutoFilter()->getColumnOffset($columnID));

                        $objWriter->startElement($column->getFilterType());
                        if ($column->getJoin() == \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column::AUTOFILTER_COLUMN_JOIN_AND) {
                            $objWriter->writeAttribute('and', 1);
                        }

                        foreach ($rules as $rule) {
                            if (($column->getFilterType() === \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column::AUTOFILTER_FILTERTYPE_FILTER) &&
                                ($rule->getOperator() === \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL) &&
                                ($rule->getValue() === '')) {
                                //    Filter rule for Blanks
                                $objWriter->writeAttribute('blank', 1);
                            } elseif ($rule->getRuleType() === \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMICFILTER) {
                                //    Dynamic Filter Rule
                                $objWriter->writeAttribute('type', $rule->getGrouping());
                                $val = $column->getAttribute('val');
                                if ($val !== null) {
                                    $objWriter->writeAttribute('val', $val);
                                }
                                $maxVal = $column->getAttribute('maxVal');
                                if ($maxVal !== null) {
                                    $objWriter->writeAttribute('maxVal', $maxVal);
                                }
                            } elseif ($rule->getRuleType() === \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_TOPTENFILTER) {
                                //    Top 10 Filter Rule
                                $objWriter->writeAttribute('val', $rule->getValue());
                                $objWriter->writeAttribute('percent', (($rule->getOperator() === \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT) ? '1' : '0'));
                                $objWriter->writeAttribute('top', (($rule->getGrouping() === \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP) ? '1' : '0'));
                            } else {
                                //    Filter, DateGroupItem or CustomFilter
                                $objWriter->startElement($rule->getRuleType());

                                if ($rule->getOperator() !== \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL) {
                                    $objWriter->writeAttribute('operator', $rule->getOperator());
                                }
                                if ($rule->getRuleType() === \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP) {
                                    // Date Group filters
                                    foreach ($rule->getValue() as $key => $value) {
                                        if ($value > '') {
                                            $objWriter->writeAttribute($key, $value);
                                        }
                                    }
                                    $objWriter->writeAttribute('dateTimeGrouping', $rule->getGrouping());
                                } else {
                                    $objWriter->writeAttribute('val', $rule->getValue());
                                }

                                $objWriter->endElement();
                            }
                        }

                        $objWriter->endElement();

                        $objWriter->endElement();
                    }
                }
            }
            $objWriter->endElement();
        }
    }

    /**
     * Write PageSetup.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writePageSetup(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // pageSetup
        $objWriter->startElement('pageSetup');
        $objWriter->writeAttribute('paperSize', $pSheet->getPageSetup()->getPaperSize());
        $objWriter->writeAttribute('orientation', $pSheet->getPageSetup()->getOrientation());

        if (!is_null($pSheet->getPageSetup()->getScale())) {
            $objWriter->writeAttribute('scale', $pSheet->getPageSetup()->getScale());
        }
        if (!is_null($pSheet->getPageSetup()->getFitToHeight())) {
            $objWriter->writeAttribute('fitToHeight', $pSheet->getPageSetup()->getFitToHeight());
        } else {
            $objWriter->writeAttribute('fitToHeight', '0');
        }
        if (!is_null($pSheet->getPageSetup()->getFitToWidth())) {
            $objWriter->writeAttribute('fitToWidth', $pSheet->getPageSetup()->getFitToWidth());
        } else {
            $objWriter->writeAttribute('fitToWidth', '0');
        }
        if (!is_null($pSheet->getPageSetup()->getFirstPageNumber())) {
            $objWriter->writeAttribute('firstPageNumber', $pSheet->getPageSetup()->getFirstPageNumber());
            $objWriter->writeAttribute('useFirstPageNumber', '1');
        }

        $objWriter->endElement();
    }

    /**
     * Write Header / Footer.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeHeaderFooter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // headerFooter
        $objWriter->startElement('headerFooter');
        $objWriter->writeAttribute('differentOddEven', ($pSheet->getHeaderFooter()->getDifferentOddEven() ? 'true' : 'false'));
        $objWriter->writeAttribute('differentFirst', ($pSheet->getHeaderFooter()->getDifferentFirst() ? 'true' : 'false'));
        $objWriter->writeAttribute('scaleWithDoc', ($pSheet->getHeaderFooter()->getScaleWithDocument() ? 'true' : 'false'));
        $objWriter->writeAttribute('alignWithMargins', ($pSheet->getHeaderFooter()->getAlignWithMargins() ? 'true' : 'false'));

        $objWriter->writeElement('oddHeader', $pSheet->getHeaderFooter()->getOddHeader());
        $objWriter->writeElement('oddFooter', $pSheet->getHeaderFooter()->getOddFooter());
        $objWriter->writeElement('evenHeader', $pSheet->getHeaderFooter()->getEvenHeader());
        $objWriter->writeElement('evenFooter', $pSheet->getHeaderFooter()->getEvenFooter());
        $objWriter->writeElement('firstHeader', $pSheet->getHeaderFooter()->getFirstHeader());
        $objWriter->writeElement('firstFooter', $pSheet->getHeaderFooter()->getFirstFooter());
        $objWriter->endElement();
    }

    /**
     * Write Breaks.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeBreaks(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // Get row and column breaks
        $aRowBreaks = [];
        $aColumnBreaks = [];
        foreach ($pSheet->getBreaks() as $cell => $breakType) {
            if ($breakType == \PhpOffice\PhpSpreadsheet\Worksheet::BREAK_ROW) {
                $aRowBreaks[] = $cell;
            } elseif ($breakType == \PhpOffice\PhpSpreadsheet\Worksheet::BREAK_COLUMN) {
                $aColumnBreaks[] = $cell;
            }
        }

        // rowBreaks
        if (!empty($aRowBreaks)) {
            $objWriter->startElement('rowBreaks');
            $objWriter->writeAttribute('count', count($aRowBreaks));
            $objWriter->writeAttribute('manualBreakCount', count($aRowBreaks));

            foreach ($aRowBreaks as $cell) {
                $coords = \PhpOffice\PhpSpreadsheet\Cell::coordinateFromString($cell);

                $objWriter->startElement('brk');
                $objWriter->writeAttribute('id', $coords[1]);
                $objWriter->writeAttribute('man', '1');
                $objWriter->endElement();
            }

            $objWriter->endElement();
        }

        // Second, write column breaks
        if (!empty($aColumnBreaks)) {
            $objWriter->startElement('colBreaks');
            $objWriter->writeAttribute('count', count($aColumnBreaks));
            $objWriter->writeAttribute('manualBreakCount', count($aColumnBreaks));

            foreach ($aColumnBreaks as $cell) {
                $coords = \PhpOffice\PhpSpreadsheet\Cell::coordinateFromString($cell);

                $objWriter->startElement('brk');
                $objWriter->writeAttribute('id', \PhpOffice\PhpSpreadsheet\Cell::columnIndexFromString($coords[0]) - 1);
                $objWriter->writeAttribute('man', '1');
                $objWriter->endElement();
            }

            $objWriter->endElement();
        }
    }

    /**
     * Write SheetData.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     * @param string[] $pStringTable String table
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeSheetData(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null, $pStringTable = null)
    {
        if (is_array($pStringTable)) {
            // Flipped stringtable, for faster index searching
            $aFlippedStringTable = $this->getParentWriter()->getWriterPart('stringtable')->flipStringTable($pStringTable);

            // sheetData
            $objWriter->startElement('sheetData');

            // Get column count
            $colCount = \PhpOffice\PhpSpreadsheet\Cell::columnIndexFromString($pSheet->getHighestColumn());

            // Highest row number
            $highestRow = $pSheet->getHighestRow();

            // Loop through cells
            $cellsByRow = [];
            foreach ($pSheet->getCellCollection() as $cellID) {
                $cellAddress = \PhpOffice\PhpSpreadsheet\Cell::coordinateFromString($cellID);
                $cellsByRow[$cellAddress[1]][] = $cellID;
            }

            $currentRow = 0;
            while ($currentRow++ < $highestRow) {
                // Get row dimension
                $rowDimension = $pSheet->getRowDimension($currentRow);

                // Write current row?
                $writeCurrentRow = isset($cellsByRow[$currentRow]) || $rowDimension->getRowHeight() >= 0 || $rowDimension->getVisible() == false || $rowDimension->getCollapsed() == true || $rowDimension->getOutlineLevel() > 0 || $rowDimension->getXfIndex() !== null;

                if ($writeCurrentRow) {
                    // Start a new row
                    $objWriter->startElement('row');
                    $objWriter->writeAttribute('r', $currentRow);
                    $objWriter->writeAttribute('spans', '1:' . $colCount);

                    // Row dimensions
                    if ($rowDimension->getRowHeight() >= 0) {
                        $objWriter->writeAttribute('customHeight', '1');
                        $objWriter->writeAttribute('ht', \PhpOffice\PhpSpreadsheet\Shared\StringHelper::formatNumber($rowDimension->getRowHeight()));
                    }

                    // Row visibility
                    if ($rowDimension->getVisible() == false) {
                        $objWriter->writeAttribute('hidden', 'true');
                    }

                    // Collapsed
                    if ($rowDimension->getCollapsed() == true) {
                        $objWriter->writeAttribute('collapsed', 'true');
                    }

                    // Outline level
                    if ($rowDimension->getOutlineLevel() > 0) {
                        $objWriter->writeAttribute('outlineLevel', $rowDimension->getOutlineLevel());
                    }

                    // Style
                    if ($rowDimension->getXfIndex() !== null) {
                        $objWriter->writeAttribute('s', $rowDimension->getXfIndex());
                        $objWriter->writeAttribute('customFormat', '1');
                    }

                    // Write cells
                    if (isset($cellsByRow[$currentRow])) {
                        foreach ($cellsByRow[$currentRow] as $cellAddress) {
                            // Write cell
                            $this->writeCell($objWriter, $pSheet, $cellAddress, $pStringTable, $aFlippedStringTable);
                        }
                    }

                    // End row
                    $objWriter->endElement();
                }
            }

            $objWriter->endElement();
        } else {
            throw new \PhpOffice\PhpSpreadsheet\Writer\Exception('Invalid parameters passed.');
        }
    }

    /**
     * Write Cell.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     * @param \PhpOffice\PhpSpreadsheet\Cell $pCellAddress Cell Address
     * @param string[] $pStringTable String table
     * @param string[] $pFlippedStringTable String table (flipped), for faster index searching
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeCell(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null, $pCellAddress = null, $pStringTable = null, $pFlippedStringTable = null)
    {
        if (is_array($pStringTable) && is_array($pFlippedStringTable)) {
            // Cell
            $pCell = $pSheet->getCell($pCellAddress);
            $objWriter->startElement('c');
            $objWriter->writeAttribute('r', $pCellAddress);

            // Sheet styles
            if ($pCell->getXfIndex() != '') {
                $objWriter->writeAttribute('s', $pCell->getXfIndex());
            }

            // If cell value is supplied, write cell value
            $cellValue = $pCell->getValue();
            if (is_object($cellValue) || $cellValue !== '') {
                // Map type
                $mappedType = $pCell->getDataType();

                // Write data type depending on its type
                switch (strtolower($mappedType)) {
                    case 'inlinestr':    // Inline string
                    case 's':            // String
                    case 'b':            // Boolean
                        $objWriter->writeAttribute('t', $mappedType);
                        break;
                    case 'f':            // Formula
                        $calculatedValue = ($this->getParentWriter()->getPreCalculateFormulas()) ?
                            $pCell->getCalculatedValue() : $cellValue;
                        if (is_string($calculatedValue)) {
                            $objWriter->writeAttribute('t', 'str');
                        }
                        break;
                    case 'e':            // Error
                        $objWriter->writeAttribute('t', $mappedType);
                }

                // Write data depending on its type
                switch (strtolower($mappedType)) {
                    case 'inlinestr':    // Inline string
                        if (!$cellValue instanceof \PhpOffice\PhpSpreadsheet\RichText) {
                            $objWriter->writeElement('t', \PhpOffice\PhpSpreadsheet\Shared\StringHelper::controlCharacterPHP2OOXML(htmlspecialchars($cellValue)));
                        } elseif ($cellValue instanceof \PhpOffice\PhpSpreadsheet\RichText) {
                            $objWriter->startElement('is');
                            $this->getParentWriter()->getWriterPart('stringtable')->writeRichText($objWriter, $cellValue);
                            $objWriter->endElement();
                        }

                        break;
                    case 's':            // String
                        if (!$cellValue instanceof \PhpOffice\PhpSpreadsheet\RichText) {
                            if (isset($pFlippedStringTable[$cellValue])) {
                                $objWriter->writeElement('v', $pFlippedStringTable[$cellValue]);
                            }
                        } elseif ($cellValue instanceof \PhpOffice\PhpSpreadsheet\RichText) {
                            $objWriter->writeElement('v', $pFlippedStringTable[$cellValue->getHashCode()]);
                        }

                        break;
                    case 'f':            // Formula
                        $attributes = $pCell->getFormulaAttributes();
                        if ($attributes['t'] == 'array') {
                            $objWriter->startElement('f');
                            $objWriter->writeAttribute('t', 'array');
                            $objWriter->writeAttribute('ref', $pCellAddress);
                            $objWriter->writeAttribute('aca', '1');
                            $objWriter->writeAttribute('ca', '1');
                            $objWriter->text(substr($cellValue, 1));
                            $objWriter->endElement();
                        } else {
                            $objWriter->writeElement('f', substr($cellValue, 1));
                        }
                        if ($this->getParentWriter()->getOffice2003Compatibility() === false) {
                            if ($this->getParentWriter()->getPreCalculateFormulas()) {
                                if (!is_array($calculatedValue) && substr($calculatedValue, 0, 1) != '#') {
                                    $objWriter->writeElement('v', \PhpOffice\PhpSpreadsheet\Shared\StringHelper::formatNumber($calculatedValue));
                                } else {
                                    $objWriter->writeElement('v', '0');
                                }
                            } else {
                                $objWriter->writeElement('v', '0');
                            }
                        }
                        break;
                    case 'n':            // Numeric
                        // force point as decimal separator in case current locale uses comma
                        $objWriter->writeElement('v', str_replace(',', '.', $cellValue));
                        break;
                    case 'b':            // Boolean
                        $objWriter->writeElement('v', ($cellValue ? '1' : '0'));
                        break;
                    case 'e':            // Error
                        if (substr($cellValue, 0, 1) == '=') {
                            $objWriter->writeElement('f', substr($cellValue, 1));
                            $objWriter->writeElement('v', substr($cellValue, 1));
                        } else {
                            $objWriter->writeElement('v', $cellValue);
                        }

                        break;
                }
            }

            $objWriter->endElement();
        } else {
            throw new \PhpOffice\PhpSpreadsheet\Writer\Exception('Invalid parameters passed.');
        }
    }

    /**
     * Write Drawings.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     * @param bool $includeCharts Flag indicating if we should include drawing details for charts
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeDrawings(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null, $includeCharts = false)
    {
        $chartCount = ($includeCharts) ? $pSheet->getChartCollection()->count() : 0;
        // If sheet contains drawings, add the relationships
        if (($pSheet->getDrawingCollection()->count() > 0) ||
            ($chartCount > 0)) {
            $objWriter->startElement('drawing');
            $objWriter->writeAttribute('r:id', 'rId1');
            $objWriter->endElement();
        }
    }

    /**
     * Write LegacyDrawing.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeLegacyDrawing(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // If sheet contains comments, add the relationships
        if (count($pSheet->getComments()) > 0) {
            $objWriter->startElement('legacyDrawing');
            $objWriter->writeAttribute('r:id', 'rId_comments_vml1');
            $objWriter->endElement();
        }
    }

    /**
     * Write LegacyDrawingHF.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pSheet Worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeLegacyDrawingHF(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, \PhpOffice\PhpSpreadsheet\Worksheet $pSheet = null)
    {
        // If sheet contains images, add the relationships
        if (count($pSheet->getHeaderFooter()->getImages()) > 0) {
            $objWriter->startElement('legacyDrawingHF');
            $objWriter->writeAttribute('r:id', 'rId_headerfooter_vml1');
            $objWriter->endElement();
        }
    }
}
