<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;

class Metadata extends WriterPart
{
    protected const METADATA_TYPES = [
        [
            'cellMeta' => '1',
            'coerce' => '1',
            'assign' => '1',
            'clearComments' => '1',
            'clearFormats' => '1',
            'rowColShift' => '1',
            'splitFirst' => '1',
            'merge' => '1',
            'pasteValues' => '1',
            'pasteAll' => '1',
            'copy' => '1',
            'minSupportedVersion' => '120000',
            'name' => 'XLDAPR',
        ],
        [
            'coerce' => '1',
            'assign' => '1',
            'clearComments' => '1',
            'clearFormats' => '1',
            'rowColShift' => '1',
            'splitFirst' => '1',
            'merge' => '1',
            'pasteValues' => '1',
            'pasteAll' => '1',
            'copy' => '1',
            'minSupportedVersion' => '120000',
            'name' => 'XLRICHVALUE',
        ],
    ];

    protected const FUTURE_METADATA = [
        'XLDAPR' => [
            [
                'uri' => '{bdbb8cdc-fa1e-496e-a857-3c3f30c029c3}',
                'xda:dynamicArrayProperties' => [
                    'fCollapsed' => '0',
                    'fDynamic' => '1',
                ],
            ],
        ],
        'XLRICHVALUE' => [
            [
                'uri' => '{3e2802c4-a4d2-4d8b-9148-e3be6c30e623}',
                'xlrd:rvb' => [
                    'i' => '0',
                ],
            ],
        ],
    ];

    protected const EXTRA_METADATA = [
        'cellMetadata' => [
            [
                'v' => '0',
                't' => '1',
            ],
        ],
        'valueMetadata' => [
            [
                'v' => '0',
                't' => '2',
            ],
        ],
    ];

    /**
     * Write metadata to XML format.
     *
     * @return string XML Output
     */
    public function writeMetadata()
    {
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        $objWriter->startElement('metadata');
        $objWriter->writeAttribute('xmlns:xda', 'http://schemas.microsoft.com/office/spreadsheetml/2017/dynamicarray');
        $objWriter->writeAttribute('xmlns:xlrd', 'http://schemas.microsoft.com/office/spreadsheetml/2017/richdata');
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $this->writeMetadataTypes($objWriter);
        $this->writeFutureMetadata($objWriter);
        $this->writeExtraMetadata($objWriter);

        $objWriter->endElement(); // metadata

        return $objWriter->getData();
    }

    private function writeMetadataTypes(XMLWriter $objWriter): void
    {
        $objWriter->startElement('metadataTypes');
        $objWriter->writeAttribute('count', (string) count(self::METADATA_TYPES));

        foreach (self::METADATA_TYPES as $metadataType) {
            $objWriter->startElement('metadataType');

            foreach ($metadataType as $metadataTypeKey => $metadataTypeValue) {
                $objWriter->writeAttribute($metadataTypeKey, $metadataTypeValue);
            }

            $objWriter->endElement(); //metadataType
        }

        $objWriter->endElement(); //metadataTypes
    }

    private function writeFutureMetadata(XMLWriter $objWriter): void
    {
        foreach (self::FUTURE_METADATA as $name => $futureMetadata) {
            $objWriter->startElement('futureMetadata');
            $objWriter->writeAttribute('count', (string) count($futureMetadata));
            $objWriter->writeAttribute('name', $name);

            foreach ($futureMetadata as $futureMetadatum) {
                $objWriter->startElement('bk');
                $objWriter->startElement('extLst');

                $ext = array_shift($futureMetadatum);
                $objWriter->startElement('ext');
                $objWriter->writeAttribute('uri', $ext);

                foreach ($futureMetadatum as $extElementName => $extElementProperties) {
                    $objWriter->startElement($extElementName);

                    foreach ($extElementProperties as $extElementPropertyName => $extElementPropertyValue) {
                        $objWriter->writeAttribute($extElementPropertyName, $extElementPropertyValue);
                    }

                    $objWriter->endElement(); // ext
                }

                $objWriter->endElement(); // ext
                $objWriter->endElement(); // extLst
                $objWriter->endElement(); // bk
            }

            $objWriter->endElement(); // futureMetadata
        }
    }

    private function writeExtraMetadata(XMLWriter $objWriter): void
    {
        foreach (self::EXTRA_METADATA as $name => $metadata) {
            $objWriter->startElement($name);
            $objWriter->writeAttribute('count', (string) count($metadata));

            foreach ($metadata as $metadatum) {
                $objWriter->startElement('bk');
                $objWriter->startElement('rc');

                foreach ($metadatum as $attributeName => $attrbuteValue) {
                    $objWriter->writeAttribute($attributeName, $attrbuteValue);
                }

                $objWriter->endElement(); // rc
                $objWriter->endElement(); // bk
            }

            $objWriter->endElement();
        }
    }
}
