<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;

class SheetViewOptions extends BaseParserClass
{
    private Worksheet $worksheet;

    private ?SimpleXMLElement $worksheetXml;

    public function __construct(Worksheet $workSheet, ?SimpleXMLElement $worksheetXml = null)
    {
        $this->worksheet = $workSheet;
        $this->worksheetXml = $worksheetXml;
    }

    public function load(bool $readDataOnly, Styles $styleReader): void
    {
        if ($this->worksheetXml === null) {
            return;
        }

        if (isset($this->worksheetXml->sheetPr)) {
            $sheetPr = $this->worksheetXml->sheetPr;
            $this->tabColor($sheetPr, $styleReader);
            $this->codeName($sheetPr);
            $this->outlines($sheetPr);
            $this->pageSetup($sheetPr);
        }

        if (isset($this->worksheetXml->sheetFormatPr)) {
            $this->sheetFormat($this->worksheetXml->sheetFormatPr);
        }

        if (!$readDataOnly && isset($this->worksheetXml->printOptions)) {
            $this->printOptions($this->worksheetXml->printOptions);
        }
    }

    private function tabColor(SimpleXMLElement $sheetPr, Styles $styleReader): void
    {
        if (isset($sheetPr->tabColor)) {
            $this->worksheet->getTabColor()->setARGB($styleReader->readColor($sheetPr->tabColor));
        }
    }

    private function codeName(SimpleXMLElement $sheetPrx): void
    {
        $sheetPr = $sheetPrx->attributes() ?? [];
        if (isset($sheetPr['codeName'])) {
            $this->worksheet->setCodeName((string) $sheetPr['codeName'], false);
        }
    }

    private function outlines(SimpleXMLElement $sheetPr): void
    {
        if (isset($sheetPr->outlinePr)) {
            $attr = $sheetPr->outlinePr->attributes() ?? [];
            if (
                isset($attr['summaryRight'])
                && !self::boolean((string) $attr['summaryRight'])
            ) {
                $this->worksheet->setShowSummaryRight(false);
            } else {
                $this->worksheet->setShowSummaryRight(true);
            }

            if (
                isset($attr['summaryBelow'])
                && !self::boolean((string) $attr['summaryBelow'])
            ) {
                $this->worksheet->setShowSummaryBelow(false);
            } else {
                $this->worksheet->setShowSummaryBelow(true);
            }
        }
    }

    private function pageSetup(SimpleXMLElement $sheetPr): void
    {
        if (isset($sheetPr->pageSetUpPr)) {
            $attr = $sheetPr->pageSetUpPr->attributes() ?? [];
            if (
                isset($attr['fitToPage'])
                && !self::boolean((string) $attr['fitToPage'])
            ) {
                $this->worksheet->getPageSetup()->setFitToPage(false);
            } else {
                $this->worksheet->getPageSetup()->setFitToPage(true);
            }
        }
    }

    private function sheetFormat(SimpleXMLElement $sheetFormatPrx): void
    {
        $sheetFormatPr = $sheetFormatPrx->attributes() ?? [];
        if (
            isset($sheetFormatPr['customHeight'])
            && self::boolean((string) $sheetFormatPr['customHeight'])
            && isset($sheetFormatPr['defaultRowHeight'])
        ) {
            $this->worksheet->getDefaultRowDimension()
                ->setRowHeight((float) $sheetFormatPr['defaultRowHeight']);
        }

        if (isset($sheetFormatPr['defaultColWidth'])) {
            $this->worksheet->getDefaultColumnDimension()
                ->setWidth((float) $sheetFormatPr['defaultColWidth']);
        }

        if (
            isset($sheetFormatPr['zeroHeight'])
            && ((string) $sheetFormatPr['zeroHeight'] === '1')
        ) {
            $this->worksheet->getDefaultRowDimension()->setZeroHeight(true);
        }
    }

    private function printOptions(SimpleXMLElement $printOptionsx): void
    {
        $printOptions = $printOptionsx->attributes() ?? [];
        if (isset($printOptions['gridLinesSet']) && self::boolean((string) $printOptions['gridLinesSet'])) {
            $this->worksheet->setShowGridlines(true);
        }
        if (isset($printOptions['gridLines']) && self::boolean((string) $printOptions['gridLines'])) {
            $this->worksheet->setPrintGridlines(true);
        }
        if (isset($printOptions['horizontalCentered']) && self::boolean((string) $printOptions['horizontalCentered'])) {
            $this->worksheet->getPageSetup()->setHorizontalCentered(true);
        }
        if (isset($printOptions['verticalCentered']) && self::boolean((string) $printOptions['verticalCentered'])) {
            $this->worksheet->getPageSetup()->setVerticalCentered(true);
        }
    }
}
