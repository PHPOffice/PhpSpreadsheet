<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;

class Hyperlinks
{
    private Worksheet $worksheet;

    /** @var string[] */
    private array $hyperlinks = [];

    public function __construct(Worksheet $workSheet)
    {
        $this->worksheet = $workSheet;
    }

    public function readHyperlinks(SimpleXMLElement $relsWorksheet): void
    {
        foreach ($relsWorksheet->children(Namespaces::RELATIONSHIPS)->Relationship as $elementx) {
            $element = Xlsx::getAttributes($elementx);
            if ($element->Type == Namespaces::HYPERLINK) {
                $this->hyperlinks[(string) $element->Id] = (string) $element->Target;
            }
        }
    }

    public function setHyperlinks(SimpleXMLElement $worksheetXml): void
    {
        foreach ($worksheetXml->children(Namespaces::MAIN)->hyperlink as $hyperlink) {
            $this->setHyperlink($hyperlink, $this->worksheet);
        }
    }

    private function setHyperlink(SimpleXMLElement $hyperlink, Worksheet $worksheet): void
    {
        // Link url
        $linkRel = Xlsx::getAttributes($hyperlink, Namespaces::SCHEMA_OFFICE_DOCUMENT);

        $attributes = Xlsx::getAttributes($hyperlink);
        foreach (Coordinate::extractAllCellReferencesInRange($attributes->ref) as $cellReference) {
            $cell = $worksheet->getCell($cellReference);
            if (isset($attributes['location'])) {
                $cell->getHyperlink()->setUrl('sheet://' . (string) $attributes['location']);
            } elseif (isset($linkRel['id'])) {
                $hyperlinkUrl = $this->hyperlinks[(string) $linkRel['id']] ?? '';
                $cell->getHyperlink()->setUrl($hyperlinkUrl);
            }

            // Tooltip
            if (isset($attributes['tooltip'])) {
                $cell->getHyperlink()->setTooltip((string) $attributes['tooltip']);
            }

            if (isset($attributes['display'])) {
                $cell->getHyperlink()->setDisplay((string) $attributes['display']);
            }
        }
    }
}
