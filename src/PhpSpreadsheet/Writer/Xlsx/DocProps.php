<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class DocProps extends WriterPart
{
    /**
     * Write docProps/app.xml to XML format.
     *
     * @return string XML Output
     */
    public function writeDocPropsApp(Spreadsheet $spreadsheet)
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

        // Properties
        $objWriter->startElement('Properties');
        $objWriter->writeAttribute('xmlns', Namespaces::EXTENDED_PROPERTIES);
        $objWriter->writeAttribute('xmlns:vt', Namespaces::PROPERTIES_VTYPES);

        // Application
        $objWriter->writeElement('Application', 'Microsoft Excel');

        // DocSecurity
        $objWriter->writeElement('DocSecurity', '0');

        // ScaleCrop
        $objWriter->writeElement('ScaleCrop', 'false');

        // HeadingPairs
        $objWriter->startElement('HeadingPairs');

        // Vector
        $objWriter->startElement('vt:vector');
        $objWriter->writeAttribute('size', '2');
        $objWriter->writeAttribute('baseType', 'variant');

        // Variant
        $objWriter->startElement('vt:variant');
        $objWriter->writeElement('vt:lpstr', 'Worksheets');
        $objWriter->endElement();

        // Variant
        $objWriter->startElement('vt:variant');
        $objWriter->writeElement('vt:i4', (string) $spreadsheet->getSheetCount());
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // TitlesOfParts
        $objWriter->startElement('TitlesOfParts');

        // Vector
        $objWriter->startElement('vt:vector');
        $objWriter->writeAttribute('size', (string) $spreadsheet->getSheetCount());
        $objWriter->writeAttribute('baseType', 'lpstr');

        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            $objWriter->writeElement('vt:lpstr', $spreadsheet->getSheet($i)->getTitle());
        }

        $objWriter->endElement();

        $objWriter->endElement();

        // Company
        $objWriter->writeElement('Company', $spreadsheet->getProperties()->getCompany());

        // Company
        $objWriter->writeElement('Manager', $spreadsheet->getProperties()->getManager());

        // LinksUpToDate
        $objWriter->writeElement('LinksUpToDate', 'false');

        // SharedDoc
        $objWriter->writeElement('SharedDoc', 'false');

        // HyperlinkBase
        $objWriter->writeElement('HyperlinkBase', $spreadsheet->getProperties()->getHyperlinkBase());

        // HyperlinksChanged
        $objWriter->writeElement('HyperlinksChanged', 'false');

        // AppVersion
        $objWriter->writeElement('AppVersion', '12.0000');

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write docProps/core.xml to XML format.
     *
     * @return string XML Output
     */
    public function writeDocPropsCore(Spreadsheet $spreadsheet)
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

        // cp:coreProperties
        $objWriter->startElement('cp:coreProperties');
        $objWriter->writeAttribute('xmlns:cp', Namespaces::CORE_PROPERTIES2);
        $objWriter->writeAttribute('xmlns:dc', Namespaces::DC_ELEMENTS);
        $objWriter->writeAttribute('xmlns:dcterms', Namespaces::DC_TERMS);
        $objWriter->writeAttribute('xmlns:dcmitype', Namespaces::DC_DCMITYPE);
        $objWriter->writeAttribute('xmlns:xsi', Namespaces::SCHEMA_INSTANCE);

        // dc:creator
        $objWriter->writeElement('dc:creator', $spreadsheet->getProperties()->getCreator());

        // cp:lastModifiedBy
        $objWriter->writeElement('cp:lastModifiedBy', $spreadsheet->getProperties()->getLastModifiedBy());

        // dcterms:created
        $objWriter->startElement('dcterms:created');
        $objWriter->writeAttribute('xsi:type', 'dcterms:W3CDTF');
        $created = $spreadsheet->getProperties()->getCreated();
        $date = Date::dateTimeFromTimestamp("$created");
        $objWriter->writeRawData($date->format(DATE_W3C));
        $objWriter->endElement();

        // dcterms:modified
        $objWriter->startElement('dcterms:modified');
        $objWriter->writeAttribute('xsi:type', 'dcterms:W3CDTF');
        $created = $spreadsheet->getProperties()->getModified();
        $date = Date::dateTimeFromTimestamp("$created");
        $objWriter->writeRawData($date->format(DATE_W3C));
        $objWriter->endElement();

        // dc:title
        $objWriter->writeElement('dc:title', $spreadsheet->getProperties()->getTitle());

        // dc:description
        $objWriter->writeElement('dc:description', $spreadsheet->getProperties()->getDescription());

        // dc:subject
        $objWriter->writeElement('dc:subject', $spreadsheet->getProperties()->getSubject());

        // cp:keywords
        $objWriter->writeElement('cp:keywords', $spreadsheet->getProperties()->getKeywords());

        // cp:category
        $objWriter->writeElement('cp:category', $spreadsheet->getProperties()->getCategory());

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write docProps/custom.xml to XML format.
     *
     * @return null|string XML Output
     */
    public function writeDocPropsCustom(Spreadsheet $spreadsheet)
    {
        $customPropertyList = $spreadsheet->getProperties()->getCustomProperties();
        if (empty($customPropertyList)) {
            return null;
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

        // cp:coreProperties
        $objWriter->startElement('Properties');
        $objWriter->writeAttribute('xmlns', Namespaces::CUSTOM_PROPERTIES);
        $objWriter->writeAttribute('xmlns:vt', Namespaces::PROPERTIES_VTYPES);

        foreach ($customPropertyList as $key => $customProperty) {
            $propertyValue = $spreadsheet->getProperties()->getCustomPropertyValue($customProperty);
            $propertyType = $spreadsheet->getProperties()->getCustomPropertyType($customProperty);

            $objWriter->startElement('property');
            $objWriter->writeAttribute('fmtid', '{D5CDD505-2E9C-101B-9397-08002B2CF9AE}');
            $objWriter->writeAttribute('pid', (string) ($key + 2));
            $objWriter->writeAttribute('name', $customProperty);

            switch ($propertyType) {
                case Properties::PROPERTY_TYPE_INTEGER:
                    $objWriter->writeElement('vt:i4', $propertyValue);

                    break;
                case Properties::PROPERTY_TYPE_FLOAT:
                    $objWriter->writeElement('vt:r8', sprintf('%F', $propertyValue));

                    break;
                case Properties::PROPERTY_TYPE_BOOLEAN:
                    $objWriter->writeElement('vt:bool', ($propertyValue) ? 'true' : 'false');

                    break;
                case Properties::PROPERTY_TYPE_DATE:
                    $objWriter->startElement('vt:filetime');
                    $date = Date::dateTimeFromTimestamp("$propertyValue");
                    $objWriter->writeRawData($date->format(DATE_W3C));
                    $objWriter->endElement();

                    break;
                default:
                    $objWriter->writeElement('vt:lpwstr', $propertyValue);

                    break;
            }

            $objWriter->endElement();
        }

        $objWriter->endElement();

        return $objWriter->getData();
    }
}
