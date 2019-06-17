<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SheetViewOptions extends BaseParserClass
{
    private $worksheetXml;

    private $worksheet;

    public function __construct(\SimpleXMLElement $worksheetXml, Worksheet $workSheet)
    {
        $this->worksheetXml = $worksheetXml;
        $this->worksheet = $workSheet;
    }

    /**
     * @param bool $readDataOnly
     */
    public function load($readDataOnly = false)
    {
        if (isset($this->worksheetXml->sheetPr)) {
            $this->tabColor();
            $this->codeName();
            $this->outlines();
            $this->pageSetup();
        }

        if (isset($this->worksheetXml->sheetFormatPr)) {
            $this->sheetFormat();
        }

        if (!$readDataOnly) {
            $this->printOptions();
        }
    }

    private function tabColor()
    {
        if (isset($this->worksheetXml->sheetPr->tabColor)) {
            if (isset($this->worksheetXml->sheetPr->tabColor['rgb'])) {
                $this->worksheet->getTabColor()->setARGB((string) $this->worksheetXml->sheetPr->tabColor['rgb']);
            }
        }
    }

    private function codeName()
    {
        if (isset($this->worksheetXml->sheetPr['codeName'])) {
            $this->worksheet->setCodeName((string) $this->worksheetXml->sheetPr['codeName'], false);
        }
    }

    private function outlines()
    {
        if (isset($this->worksheetXml->sheetPr->outlinePr)) {
            if (isset($this->worksheetXml->sheetPr->outlinePr['summaryRight']) &&
                !self::boolean((string) $this->worksheetXml->sheetPr->outlinePr['summaryRight'])) {
                $this->worksheet->setShowSummaryRight(false);
            } else {
                $this->worksheet->setShowSummaryRight(true);
            }

            if (isset($this->worksheetXml->sheetPr->outlinePr['summaryBelow']) &&
                !self::boolean((string) $this->worksheetXml->sheetPr->outlinePr['summaryBelow'])) {
                $this->worksheet->setShowSummaryBelow(false);
            } else {
                $this->worksheet->setShowSummaryBelow(true);
            }
        }
    }

    private function pageSetup()
    {
        if (isset($this->worksheetXml->sheetPr->pageSetUpPr)) {
            if (isset($this->worksheetXml->sheetPr->pageSetUpPr['fitToPage']) &&
                !self::boolean((string) $this->worksheetXml->sheetPr->pageSetUpPr['fitToPage'])) {
                $this->worksheet->getPageSetup()->setFitToPage(false);
            } else {
                $this->worksheet->getPageSetup()->setFitToPage(true);
            }
        }
    }

    private function sheetFormat()
    {
        if (isset($this->worksheetXml->sheetFormatPr['customHeight']) &&
            self::boolean((string) $this->worksheetXml->sheetFormatPr['customHeight']) &&
            isset($this->worksheetXml->sheetFormatPr['defaultRowHeight'])) {
            $this->worksheet->getDefaultRowDimension()
                ->setRowHeight((float) $this->worksheetXml->sheetFormatPr['defaultRowHeight']);
        }

        if (isset($this->worksheetXml->sheetFormatPr['defaultColWidth'])) {
            $this->worksheet->getDefaultColumnDimension()
                ->setWidth((float) $this->worksheetXml->sheetFormatPr['defaultColWidth']);
        }

        if (isset($this->worksheetXml->sheetFormatPr['zeroHeight']) &&
            ((string) $this->worksheetXml->sheetFormatPr['zeroHeight'] == '1')) {
            $this->worksheet->getDefaultRowDimension()->setZeroHeight(true);
        }
    }

    private function printOptions()
    {
        if (isset($this->worksheetXml->printOptions)) {
            if (self::boolean((string) $this->worksheetXml->printOptions['gridLinesSet'])) {
                $this->worksheet->setShowGridlines(true);
            }
            if (self::boolean((string) $this->worksheetXml->printOptions['gridLines'])) {
                $this->worksheet->setPrintGridlines(true);
            }
            if (self::boolean((string) $this->worksheetXml->printOptions['horizontalCentered'])) {
                $this->worksheet->getPageSetup()->setHorizontalCentered(true);
            }
            if (self::boolean((string) $this->worksheetXml->printOptions['verticalCentered'])) {
                $this->worksheet->getPageSetup()->setVerticalCentered(true);
            }
        }
    }
}
