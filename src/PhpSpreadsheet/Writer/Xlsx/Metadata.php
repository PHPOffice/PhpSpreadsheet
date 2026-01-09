<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;

class Metadata extends WriterPart
{
    /**
     * Write content types to XML format.
     *
     * @return string XML Output
     */
    public function writeMetadata(int $richDataCount = 0): string
    {
        if (!$this->getParentWriter()->useDynamicArrays() && $richDataCount === 0) {
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
        $objWriter->startElement('metadata');
        $objWriter->writeAttribute('xmlns', Namespaces::MAIN);
        $objWriter->writeAttribute('xmlns:xlrd', Namespaces::DYNAMIC_ARRAY_RICHDATA);

        if (!$this->getParentWriter()->useDynamicArrays()) {
            $objWriter->startElement('metadataTypes');
            $objWriter->writeAttribute('count', '1');
            $this->writeMetadataType($objWriter, 'XLRICHVALUE', false);
            $objWriter->endElement(); // metadataTypes

            $this->writeFutureMetadataXLRICHVALUE($objWriter, $richDataCount);
            $this->writeValueMetadata($objWriter, $richDataCount);
        } else {
            $objWriter->writeAttribute('xmlns:xda', Namespaces::DYNAMIC_ARRAY);
            $objWriter->startElement('metadataTypes');
            $objWriter->writeAttribute('count', '2');
            $this->writeMetadataType($objWriter, 'XLDAPR');
            $this->writeMetadataType($objWriter, 'XLRICHVALUE', false);
            $objWriter->endElement(); // metadataTypes

            $this->writeFutureMetadataXLDAPR($objWriter, 1);
            $this->writeFutureMetadataXLRICHVALUE($objWriter, $richDataCount ?: 1);
            $this->writeCellMetadata($objWriter, 1);
            $this->writeValueMetadata($objWriter, ($richDataCount === 0) ? 1 : $richDataCount, 2);
        }

        $objWriter->endElement(); // metadata

        // Return
        return $objWriter->getData();
    }

    private function writeMetadataType(XMLWriter $objWriter, string $name, bool $cellMeta = true): void
    {
        $objWriter->startElement('metadataType');
        $objWriter->writeAttribute('name', $name);
        $objWriter->writeAttribute('minSupportedVersion', '120000');
        $objWriter->writeAttribute('copy', '1');
        $objWriter->writeAttribute('pasteAll', '1');
        $objWriter->writeAttribute('pasteValues', '1');
        $objWriter->writeAttribute('merge', '1');
        $objWriter->writeAttribute('splitFirst', '1');
        $objWriter->writeAttribute('rowColShift', '1');
        $objWriter->writeAttribute('clearFormats', '1');
        $objWriter->writeAttribute('clearComments', '1');
        $objWriter->writeAttribute('assign', '1');
        $objWriter->writeAttribute('coerce', '1');

        if ($cellMeta) {
            $objWriter->writeAttribute('cellMeta', '1');
        }
        $objWriter->endElement();
    }

    private function writeFutureMetadataXLDAPR(XMLWriter $objWriter, int $count = 1): void
    {
        $objWriter->startElement('futureMetadata');
        $objWriter->writeAttribute('name', 'XLDAPR');
        $objWriter->writeAttribute('count', (string) $count);

        for ($index = 0; $index < $count; ++$index) {
            $objWriter->startElement('bk');
            $objWriter->startElement('extLst');
            $objWriter->startElement('ext');
            $objWriter->writeAttribute('uri', '{bdbb8cdc-fa1e-496e-a857-3c3f30c029c3}');
            $objWriter->startElement('xda:dynamicArrayProperties');
            $objWriter->writeAttribute('fDynamic', '1');
            $objWriter->writeAttribute('fCollapsed', '0');
            $objWriter->endElement(); // xda:dynamicArrayProperties
            $objWriter->endElement(); // ext
            $objWriter->endElement(); // extLst
            $objWriter->endElement(); // bk
        }
        $objWriter->endElement(); // futureMetadata XLDAPR
    }

    private function writeFutureMetadataXLRICHVALUE(XMLWriter $objWriter, int $count): void
    {
        $objWriter->startElement('futureMetadata');
        $objWriter->writeAttribute('name', 'XLRICHVALUE');
        $objWriter->writeAttribute('count', (string) $count);

        for ($index = 0; $index < $count; ++$index) {
            $objWriter->startElement('bk');
            $objWriter->startElement('extLst');
            $objWriter->startElement('ext');
            $objWriter->writeAttribute('uri', '{3e2802c4-a4d2-4d8b-9148-e3be6c30e623}');
            $objWriter->startElement('xlrd:rvb');
            $objWriter->writeAttribute('i', (string) $index);
            $objWriter->endElement(); // xlrd:rvb
            $objWriter->endElement(); // ext
            $objWriter->endElement(); // extLst
            $objWriter->endElement(); // bk
        }
        $objWriter->endElement(); // futureMetadata XLRICHVALUE
    }

    private function writeCellMetadata(XMLWriter $objWriter, int $count = 1, int $t = 1): void
    {
        $objWriter->startElement('cellMetadata');
        $objWriter->writeAttribute('count', (string) $count);

        for ($index = 0; $index < $count; ++$index) {
            $objWriter->startElement('bk');
            $objWriter->startElement('rc');
            $objWriter->writeAttribute('t', (string) $t);
            $objWriter->writeAttribute('v', (string) $index);
            $objWriter->endElement(); // rc
            $objWriter->endElement(); // bk
        }
        $objWriter->endElement(); // cellMetadata
    }

    private function writeValueMetadata(XMLWriter $objWriter, int $count = 1, int $t = 1): void
    {
        $objWriter->startElement('valueMetadata');
        $objWriter->writeAttribute('count', (string) $count);

        for ($index = 0; $index < $count; ++$index) {
            $objWriter->startElement('bk');
            $objWriter->startElement('rc');
            $objWriter->writeAttribute('t', (string) $t);
            $objWriter->writeAttribute('v', (string) $index);
            $objWriter->endElement(); // rc
            $objWriter->endElement(); // bk
        }
        $objWriter->endElement(); // valueMetadata
    }
}
