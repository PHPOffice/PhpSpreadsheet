<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RichDataDrawing
{
    /** @var Drawing[] */
    private array $drawings = [];

    /**
     * Generate all Rich Data XML files.
     *
     * @return array<string,string> [path => XML content]
     */
    public function generateFiles(Spreadsheet $spreadsheet): array
    {
        $worksheetCount = $spreadsheet->getSheetCount();

        $index = 0;
        for ($i = 0; $i < $worksheetCount; ++$i) {
            $worksheet = $spreadsheet->getSheet($i);
            $iterator = $worksheet->getInCellDrawingCollection()->getIterator();
            while ($iterator->valid()) {
                /** @var Drawing $pDrawing */
                $pDrawing = $iterator->current();
                $indexedFilename = $pDrawing->getIndexedFilename();
                if (!isset($this->drawings[$indexedFilename])) {
                    $pDrawing->setIndex(++$index);
                    $this->drawings[$indexedFilename] = $pDrawing;
                } else {
                    $pDrawing->setIndex($this->drawings[$indexedFilename]->getIndex());
                }
                $iterator->next();
            }
        }

        return (count($this->drawings) === 0) ? [] : [
            'xl/richData/rdrichvalue.xml' => $this->writeRdrichvalueXML(),
            'xl/richData/rdrichvaluestructure.xml' => $this->writeRdrichvaluestructureXML(),
            'xl/richData/rdRichValueTypes.xml' => $this->writeRdRichValueTypesXML(),
            'xl/richData/richValueRel.xml' => $this->writeRichValueRelXML(),
            'xl/richData/_rels/richValueRel.xml.rels' => $this->writeRichValueRelRelsXML(),
        ];
    }

    /**
     * @return Drawing[]
     */
    public function getDrawings(): array
    {
        return $this->drawings;
    }

    private function writeRdrichvalueXML(): string
    {
        $xml = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        $xml->startDocument('1.0', 'UTF-8', 'yes');
        $xml->startElement('rvData');
        $xml->writeAttribute('xmlns', 'http://schemas.microsoft.com/office/spreadsheetml/2017/richdata');
        $xml->writeAttribute('count', (string) count($this->drawings));

        $index = 0;
        foreach ($this->drawings as $drawing) {
            $xml->startElement('rv');
            $xml->writeAttribute('s', '0');
            $xml->writeElement('v', (string) $index++);
            $xml->writeElement('v', '5');
            $xml->endElement(); // rv
        }

        $xml->endElement(); // rvData

        return $xml->getData();
    }

    private function writeRdrichvaluestructureXML(): string
    {
        $xml = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        $xml->startDocument('1.0', 'UTF-8', 'yes');
        $xml->startElement('rvStructures');
        $xml->writeAttribute('xmlns', 'http://schemas.microsoft.com/office/spreadsheetml/2017/richdata');
        $xml->writeAttribute('count', '1');

        $xml->startElement('s');
        $xml->writeAttribute('t', '_localImage');

        $xml->startElement('k');
        $xml->writeAttribute('n', '_rvRel:LocalImageIdentifier');
        $xml->writeAttribute('t', 'i');
        $xml->endElement();

        $xml->startElement('k');
        $xml->writeAttribute('n', 'CalcOrigin');
        $xml->writeAttribute('t', 'i');
        $xml->endElement();

        $xml->endElement(); // s

        $xml->endElement(); // rvStructures

        return $xml->getData();
    }

    private function writeRdRichValueTypesXML(): string
    {
        $xml = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        $xml->startDocument('1.0', 'UTF-8', 'yes');
        $xml->startElement('rvTypesInfo');
        $xml->writeAttribute('xmlns', 'http://schemas.microsoft.com/office/spreadsheetml/2017/richdata2');
        $xml->writeAttribute('xmlns:mc', 'http://schemas.openxmlformats.org/markup-compatibility/2006');
        $xml->writeAttribute('mc:Ignorable', 'x');
        $xml->writeAttribute('xmlns:x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $xml->startElement('global');
        $xml->startElement('keyFlags');

        $keys = [
            '_Self', '_DisplayString', '_Flags', '_Format',
            '_SubLabel', '_Attribution', '_Icon', '_Display',
            '_CanonicalPropertyNames', '_ClassificationId',
        ];

        foreach ($keys as $key) {
            $xml->startElement('key');
            $xml->writeAttribute('name', $key);

            $xml->startElement('flag');
            $xml->writeAttribute('name', 'ExcludeFromCalcComparison');
            $xml->writeAttribute('value', '1');
            if ($key === '_Self') {
                $xml->startElement('flag');
                $xml->writeAttribute('name', 'ExcludeFromFile');
                $xml->writeAttribute('value', '1');
                $xml->endElement();
            }
            $xml->endElement(); // flag
            $xml->endElement(); // key
        }

        $xml->endElement(); // keyFlags
        $xml->endElement(); // global
        $xml->endElement(); // rvTypesInfo

        return $xml->getData();
    }

    private function writeRichValueRelXML(): string
    {
        $xml = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        $xml->startDocument('1.0', 'UTF-8', 'yes');
        $xml->startElement('richValueRels');
        $xml->writeAttribute('xmlns', 'http://schemas.microsoft.com/office/spreadsheetml/2022/richvaluerel');
        $xml->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

        $index = 0;
        foreach ($this->drawings as $drawing) {
            $xml->startElement('rel');
            $xml->writeAttribute('r:id', 'rId' . ++$index);
            $xml->endElement();
        }

        $xml->endElement(); // richValueRels

        return $xml->getData();
    }

    private function writeRichValueRelRelsXML(): string
    {
        $xml = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        $xml->startDocument('1.0', 'UTF-8', 'yes');
        $xml->startElement('Relationships');
        $xml->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

        $index = 0;
        foreach ($this->drawings as $drawing) {
            $xml->startElement('Relationship');
            $xml->writeAttribute('Id', 'rId' . ++$index);
            $xml->writeAttribute('Type', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image');
            $xml->writeAttribute('Target', '../media/' . $drawing->getIndexedFilename());
            $xml->endElement();
        }

        $xml->endElement(); // Relationships

        return $xml->getData();
    }
}
