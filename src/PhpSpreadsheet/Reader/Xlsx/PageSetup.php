<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;

class PageSetup extends BaseParserClass
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

    public function load(array $unparsedLoadedData)
    {
        if (empty($this->xmlMap)) {
            return $unparsedLoadedData;
        }

        $this->margins($this->xmlMap, $this->worksheet);
        $unparsedLoadedData = $this->pageSetup($this->xmlMap, $this->worksheet, $unparsedLoadedData);
        $this->headerFooter($this->xmlMap, $this->worksheet);
        $this->pageBreaks($this->xmlMap, $this->worksheet);

        return $unparsedLoadedData;
    }

    private function margins(array $xmlMap, Worksheet $worksheet): void
    {
        if (!empty($xmlMap[SheetStructure::PAGE_MARGINS])) {
            $pageMargins = XmlParser::loadXml($this->securityScanner, reset($xmlMap[SheetStructure::PAGE_MARGINS]));

            $docPageMargins = $worksheet->getPageMargins();
            $docPageMargins->setLeft((float) ($pageMargins['left']));
            $docPageMargins->setRight((float) ($pageMargins['right']));
            $docPageMargins->setTop((float) ($pageMargins['top']));
            $docPageMargins->setBottom((float) ($pageMargins['bottom']));
            $docPageMargins->setHeader((float) ($pageMargins['header']));
            $docPageMargins->setFooter((float) ($pageMargins['footer']));
        }
    }

    private function pageSetup(array $xmlMap, Worksheet $worksheet, array $unparsedLoadedData)
    {
        if (!empty($xmlMap[SheetStructure::PAGE_SETUP])) {
            $pageSetup = XmlParser::loadXml($this->securityScanner, reset($xmlMap[SheetStructure::PAGE_SETUP]));
            $docPageSetup = $worksheet->getPageSetup();

            if (isset($pageSetup['orientation'])) {
                $docPageSetup->setOrientation((string) $pageSetup['orientation']);
            }
            if (isset($pageSetup['paperSize'])) {
                $docPageSetup->setPaperSize((int) ($pageSetup['paperSize']));
            }
            if (isset($pageSetup['scale'])) {
                $docPageSetup->setScale((int) ($pageSetup['scale']), false);
            }
            if (isset($pageSetup['fitToHeight']) && (int) ($pageSetup['fitToHeight']) >= 0) {
                $docPageSetup->setFitToHeight((int) ($pageSetup['fitToHeight']), false);
            }
            if (isset($pageSetup['fitToWidth']) && (int) ($pageSetup['fitToWidth']) >= 0) {
                $docPageSetup->setFitToWidth((int) ($pageSetup['fitToWidth']), false);
            }
            if (
                isset($pageSetup['firstPageNumber'], $pageSetup['useFirstPageNumber']) &&
                self::boolean((string) $pageSetup['useFirstPageNumber'])
            ) {
                $docPageSetup->setFirstPageNumber((int) ($pageSetup['firstPageNumber']));
            }
            if (isset($pageSetup['pageOrder'])) {
                $docPageSetup->setPageOrder((string) $pageSetup['pageOrder']);
            }

            $relAttributes = $pageSetup->attributes(Namespaces::SCHEMA_OFFICE_DOCUMENT);
            if (isset($relAttributes['id'])) {
                $unparsedLoadedData['sheets'][$worksheet->getCodeName()]['pageSetupRelId'] = (string) $relAttributes['id'];
            }
        }

        return $unparsedLoadedData;
    }

    private function headerFooter(array $xmlMap, Worksheet $worksheet): void
    {
        if (!empty($xmlMap[SheetStructure::HEADER_FOOTER])) {
            $headerFooter = XmlParser::loadXml($this->securityScanner, reset($xmlMap[SheetStructure::HEADER_FOOTER]));
            $docHeaderFooter = $worksheet->getHeaderFooter();

            if (
                isset($headerFooter['differentOddEven']) &&
                self::boolean((string) $headerFooter['differentOddEven'])
            ) {
                $docHeaderFooter->setDifferentOddEven(true);
            } else {
                $docHeaderFooter->setDifferentOddEven(false);
            }
            if (
                isset($headerFooter['differentFirst']) &&
                self::boolean((string) $headerFooter['differentFirst'])
            ) {
                $docHeaderFooter->setDifferentFirst(true);
            } else {
                $docHeaderFooter->setDifferentFirst(false);
            }
            if (
                isset($headerFooter['scaleWithDoc']) &&
                !self::boolean((string) $headerFooter['scaleWithDoc'])
            ) {
                $docHeaderFooter->setScaleWithDocument(false);
            } else {
                $docHeaderFooter->setScaleWithDocument(true);
            }
            if (
                isset($headerFooter['alignWithMargins']) &&
                !self::boolean((string) $headerFooter['alignWithMargins'])
            ) {
                $docHeaderFooter->setAlignWithMargins(false);
            } else {
                $docHeaderFooter->setAlignWithMargins(true);
            }

            $docHeaderFooter->setOddHeader((string) $headerFooter->oddHeader);
            $docHeaderFooter->setOddFooter((string) $headerFooter->oddFooter);
            $docHeaderFooter->setEvenHeader((string) $headerFooter->evenHeader);
            $docHeaderFooter->setEvenFooter((string) $headerFooter->evenFooter);
            $docHeaderFooter->setFirstHeader((string) $headerFooter->firstHeader);
            $docHeaderFooter->setFirstFooter((string) $headerFooter->firstFooter);
        }
    }

    private function pageBreaks(array $xmlMap, Worksheet $worksheet): void
    {
        if (!empty($xmlMap[SheetStructure::ROW_BREAKS])) {
            $rowBreaks = XmlParser::loadXml($this->securityScanner, reset($xmlMap[SheetStructure::ROW_BREAKS]));
            $this->rowBreaks($rowBreaks, $worksheet);
        }

        if (!empty($xmlMap[SheetStructure::COL_BREAKS])) {
            $colBreaks = XmlParser::loadXml($this->securityScanner, reset($xmlMap[SheetStructure::COL_BREAKS]));
            $this->columnBreaks($colBreaks, $worksheet);
        }
    }

    private function rowBreaks(SimpleXMLElement $rowBreaks, Worksheet $worksheet): void
    {
        if (!$rowBreaks->brk) {
            return;
        }

        foreach ($rowBreaks->brk as $brk) {
            if ($brk['man']) {
                $worksheet->setBreak("A{$brk['id']}", Worksheet::BREAK_ROW);
            }
        }
    }

    private function columnBreaks(SimpleXMLElement $colBreaks, Worksheet $worksheet): void
    {
        if (!$colBreaks->brk) {
            return;
        }

        foreach ($colBreaks->brk as $brk) {
            if ($brk['man']) {
                $worksheet->setBreak(
                    Coordinate::stringFromColumnIndex(((int) $brk['id']) + 1) . '1',
                    Worksheet::BREAK_COLUMN
                );
            }
        }
    }
}
