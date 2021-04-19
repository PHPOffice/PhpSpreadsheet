<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;

class Settings extends WriterPart
{
    /**
     * Write settings.xml to XML format.
     *
     * @return string XML Output
     */
    public function write(): string
    {
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8');

        // Settings
        $objWriter->startElement('office:document-settings');
        $objWriter->writeAttribute('xmlns:office', 'urn:oasis:names:tc:opendocument:xmlns:office:1.0');
        $objWriter->writeAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $objWriter->writeAttribute('xmlns:config', 'urn:oasis:names:tc:opendocument:xmlns:config:1.0');
        $objWriter->writeAttribute('xmlns:ooo', 'http://openoffice.org/2004/office');
        $objWriter->writeAttribute('office:version', '1.2');

        $objWriter->startElement('office:settings');
        $objWriter->startElement('config:config-item-set');
        $objWriter->writeAttribute('config:name', 'ooo:view-settings');
        $objWriter->startElement('config:config-item-map-indexed');
        $objWriter->writeAttribute('config:name', 'Views');
        $objWriter->startElement('config:config-item-map-entry');
        $spreadsheet = $this->getParentWriter()->getSpreadsheet();

        $objWriter->startElement('config:config-item');
        $objWriter->writeAttribute('config:name', 'ViewId');
        $objWriter->writeAttribute('config:type', 'string');
        $objWriter->text('view1');
        $objWriter->endElement(); // ViewId
        $objWriter->startElement('config:config-item-map-named');
        $objWriter->writeAttribute('config:name', 'Tables');
        foreach ($spreadsheet->getWorksheetIterator() as $ws) {
            $objWriter->startElement('config:config-item-map-entry');
            $objWriter->writeAttribute('config:name', $ws->getTitle());
            $selected = $ws->getSelectedCells();
            if (preg_match('/^([a-z]+)([0-9]+)/i', $selected, $matches) === 1) {
                $colSel = Coordinate::columnIndexFromString($matches[1]) - 1;
                $rowSel = (int) $matches[2] - 1;
                $objWriter->startElement('config:config-item');
                $objWriter->writeAttribute('config:name', 'CursorPositionX');
                $objWriter->writeAttribute('config:type', 'int');
                $objWriter->text($colSel);
                $objWriter->endElement();
                $objWriter->startElement('config:config-item');
                $objWriter->writeAttribute('config:name', 'CursorPositionY');
                $objWriter->writeAttribute('config:type', 'int');
                $objWriter->text($rowSel);
                $objWriter->endElement();
            }
            $objWriter->endElement(); // config:config-item-map-entry
        }
        $objWriter->endElement(); // config:config-item-map-named
        $wstitle = $spreadsheet->getActiveSheet()->getTitle();
        $objWriter->startElement('config:config-item');
        $objWriter->writeAttribute('config:name', 'ActiveTable');
        $objWriter->writeAttribute('config:type', 'string');
        $objWriter->text($wstitle);
        $objWriter->endElement(); // config:config-item ActiveTable

        $objWriter->endElement(); // config:config-item-map-entry
        $objWriter->endElement(); // config:config-item-map-indexed Views
        $objWriter->endElement(); // config:config-item-set ooo:view-settings
        $objWriter->startElement('config:config-item-set');
        $objWriter->writeAttribute('config:name', 'ooo:configuration-settings');
        $objWriter->endElement(); // config:config-item-set ooo:configuration-settings
        $objWriter->endElement(); // office:settings
        $objWriter->endElement(); // office:document-settings

        return $objWriter->getData();
    }
}
