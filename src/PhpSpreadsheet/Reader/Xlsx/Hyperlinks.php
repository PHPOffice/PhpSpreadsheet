<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;

class Hyperlinks
{
    private $worksheet;

    private $hyperlinks = [];

    public function __construct(Worksheet $workSheet)
    {
        $this->worksheet = $workSheet;
    }

    public function readHyperlinks(SimpleXMLElement $relsWorksheet): void
    {
        foreach ($relsWorksheet->Relationship as $element) {
            if ($element['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink') {
                $this->hyperlinks[(string) $element['Id']] = (string) $element['Target'];
            }
        }
    }

    public function setHyperlinks(SimpleXMLElement $worksheetXml): void
    {
        foreach ($worksheetXml->hyperlink as $hyperlink) {
            $this->setHyperlink($hyperlink, $this->worksheet);
        }
    }

    private function setHyperlink(SimpleXMLElement $hyperlink, Worksheet $worksheet): void
    {
        // Link url
        $linkRel = $hyperlink->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');

        foreach (Coordinate::extractAllCellReferencesInRange($hyperlink['ref']) as $cellReference) {
            $cell = $worksheet->getCell($cellReference);
            if (isset($linkRel['id'])) {
                $hyperlinkUrl = $this->hyperlinks[(string) $linkRel['id']];
                if (isset($hyperlink['location'])) {
                    $hyperlinkUrl .= '#' . (string) $hyperlink['location'];
                }
                $cell->getHyperlink()->setUrl($hyperlinkUrl);
            } elseif (isset($hyperlink['location'])) {
                $cell->getHyperlink()->setUrl('sheet://' . (string) $hyperlink['location']);
            }

            // Tooltip
            if (isset($hyperlink['tooltip'])) {
                $cell->getHyperlink()->setTooltip((string) $hyperlink['tooltip']);
            }
        }
    }
}
