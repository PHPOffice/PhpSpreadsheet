<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;

class SheetViewOptions extends BaseParserClass
{
    private $worksheet;

    private $xmlMap;

    private $securityScanner;

    public function __construct(Worksheet $workSheet, array $xmlMap, XmlScanner $securityScanner)
    {
        $this->worksheet = $workSheet;
        $this->xmlMap = $xmlMap;
        $this->securityScanner = $securityScanner;
    }

    public function load(bool $readDataOnly, Styles $styleReader): void
    {
        if (!empty($this->xmlMap[SheetStructure::SHEET_PR])) {
            $sheetPr = XmlParser::loadXml($this->securityScanner, reset($this->xmlMap[SheetStructure::SHEET_PR]));

            $this->tabColor($sheetPr, $styleReader);
            $this->codeName($sheetPr);
            $this->outlines($sheetPr);
            $this->pageSetup($sheetPr);
        }

        if (!empty($this->xmlMap[SheetStructure::SHEET_FORMAT_PR])) {
            $sheetFormatPr = XmlParser::loadXml($this->securityScanner, reset($this->xmlMap[SheetStructure::SHEET_FORMAT_PR]));

            $this->sheetFormat($sheetFormatPr);
        }

        if (!$readDataOnly && !empty($this->xmlMap[SheetStructure::PRINT_OPTIONS])) {
            $printOptions = XmlParser::loadXml($this->securityScanner, reset($this->xmlMap[SheetStructure::PRINT_OPTIONS]));

            $this->printOptions($printOptions);
        }
    }

    private function tabColor(SimpleXMLElement $sheetPr, Styles $styleReader): void
    {
        if (isset($sheetPr->tabColor)) {
            $this->worksheet->getTabColor()->setARGB($styleReader->readColor($sheetPr->tabColor));
        }
    }

    private function codeName(SimpleXMLElement $sheetPr): void
    {
        if (isset($sheetPr['codeName'])) {
            $this->worksheet->setCodeName((string) $sheetPr['codeName'], false);
        }
    }

    private function outlines(SimpleXMLElement $sheetPr): void
    {
        if (isset($sheetPr->outlinePr)) {
            if (
                isset($sheetPr->outlinePr['summaryRight']) &&
                !self::boolean((string) $sheetPr->outlinePr['summaryRight'])
            ) {
                $this->worksheet->setShowSummaryRight(false);
            } else {
                $this->worksheet->setShowSummaryRight(true);
            }

            if (
                isset($sheetPr->outlinePr['summaryBelow']) &&
                !self::boolean((string) $sheetPr->outlinePr['summaryBelow'])
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
            if (
                isset($sheetPr->pageSetUpPr['fitToPage']) &&
                !self::boolean((string) $sheetPr->pageSetUpPr['fitToPage'])
            ) {
                $this->worksheet->getPageSetup()->setFitToPage(false);
            } else {
                $this->worksheet->getPageSetup()->setFitToPage(true);
            }
        }
    }

    private function sheetFormat(SimpleXMLElement $sheetFormatPr): void
    {
        if (
            isset($sheetFormatPr['customHeight']) &&
            self::boolean((string) $sheetFormatPr['customHeight']) &&
            isset($sheetFormatPr['defaultRowHeight'])
        ) {
            $this->worksheet->getDefaultRowDimension()
                ->setRowHeight((float) $sheetFormatPr['defaultRowHeight']);
        }

        if (isset($sheetFormatPr['defaultColWidth'])) {
            $this->worksheet->getDefaultColumnDimension()
                ->setWidth((float) $sheetFormatPr['defaultColWidth']);
        }

        if (
            isset($sheetFormatPr['zeroHeight']) &&
            ((string) $sheetFormatPr['zeroHeight'] === '1')
        ) {
            $this->worksheet->getDefaultRowDimension()->setZeroHeight(true);
        }
    }

    private function printOptions(SimpleXMLElement $printOptions): void
    {
        if (self::boolean((string) $printOptions['gridLinesSet'])) {
            $this->worksheet->setShowGridlines(true);
        }
        if (self::boolean((string) $printOptions['gridLines'])) {
            $this->worksheet->setPrintGridlines(true);
        }
        if (self::boolean((string) $printOptions['horizontalCentered'])) {
            $this->worksheet->getPageSetup()->setHorizontalCentered(true);
        }
        if (self::boolean((string) $printOptions['verticalCentered'])) {
            $this->worksheet->getPageSetup()->setVerticalCentered(true);
        }
    }
}
