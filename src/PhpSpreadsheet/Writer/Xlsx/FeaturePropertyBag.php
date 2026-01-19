<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class FeaturePropertyBag extends WriterPart
{
    public function writeFeaturePropertyBag(Spreadsheet $spreadsheet): string
    {
        if (!$spreadsheet->getUsesCheckBoxStyle()) {
            return '';
        }
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // Types
        $objWriter->startElement('FeaturePropertyBags');
        $objWriter->writeAttribute('xmlns', Namespaces::FEATURE_PROPERTY_BAG);

        $objWriter->startElement('bag');
        $objWriter->writeAttribute('type', 'Checkbox');
        $objWriter->endElement(); // bag type=Checkbox

        $objWriter->startElement('bag');
        $objWriter->writeAttribute('type', 'XFControls');
        $objWriter->startElement('bagId');
        $objWriter->writeAttribute('k', 'CellControl');
        $objWriter->text('0');
        $objWriter->endElement(); // bagid
        $objWriter->endElement(); // bag type=XFControls

        $objWriter->startElement('bag');
        $objWriter->writeAttribute('type', 'XFComplement');
        $objWriter->startElement('bagId');
        $objWriter->writeAttribute('k', 'XFControls');
        $objWriter->text('1');
        $objWriter->endElement(); // bagid
        $objWriter->endElement(); // bag type=XFComplement

        $objWriter->startElement('bag');
        $objWriter->writeAttribute('type', 'XFComplements');
        $objWriter->writeAttribute('extRef', 'XFComplementsMapperExtRef');
        $objWriter->startElement('a');
        $objWriter->writeAttribute('k', 'MappedFeaturePropertyBags');
        $objWriter->startElement('bagId');
        $objWriter->text('2');
        $objWriter->endElement(); // bagid
        $objWriter->endElement(); // a
        $objWriter->endElement(); // bag type=XFComplements

        $objWriter->endElement(); // FeaturePropertyBags

        return $objWriter->getData();
    }
}
