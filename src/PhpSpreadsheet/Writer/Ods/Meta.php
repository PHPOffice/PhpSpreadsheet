<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Meta extends WriterPart
{
    /**
     * Write meta.xml to XML format.
     *
     * @return string XML Output
     */
    public function write(): string
    {
        $spreadsheet = $this->getParentWriter()->getSpreadsheet();

        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8');

        // Meta
        $objWriter->startElement('office:document-meta');

        $objWriter->writeAttribute('xmlns:office', 'urn:oasis:names:tc:opendocument:xmlns:office:1.0');
        $objWriter->writeAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $objWriter->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $objWriter->writeAttribute('xmlns:meta', 'urn:oasis:names:tc:opendocument:xmlns:meta:1.0');
        $objWriter->writeAttribute('xmlns:ooo', 'http://openoffice.org/2004/office');
        $objWriter->writeAttribute('xmlns:grddl', 'http://www.w3.org/2003/g/data-view#');
        $objWriter->writeAttribute('office:version', '1.2');

        $objWriter->startElement('office:meta');

        $objWriter->writeElement('meta:initial-creator', $spreadsheet->getProperties()->getCreator());
        $objWriter->writeElement('dc:creator', $spreadsheet->getProperties()->getCreator());
        $created = $spreadsheet->getProperties()->getCreated();
        $date = Date::dateTimeFromTimestamp("$created");
        $date->setTimeZone(Date::getDefaultOrLocalTimeZone());
        $objWriter->writeElement('meta:creation-date', $date->format(DATE_W3C));
        $created = $spreadsheet->getProperties()->getModified();
        $date = Date::dateTimeFromTimestamp("$created");
        $date->setTimeZone(Date::getDefaultOrLocalTimeZone());
        $objWriter->writeElement('dc:date', $date->format(DATE_W3C));
        $objWriter->writeElement('dc:title', $spreadsheet->getProperties()->getTitle());
        $objWriter->writeElement('dc:description', $spreadsheet->getProperties()->getDescription());
        $objWriter->writeElement('dc:subject', $spreadsheet->getProperties()->getSubject());
        $objWriter->writeElement('meta:keyword', $spreadsheet->getProperties()->getKeywords());
        // Don't know if this changed over time, but the keywords are all
        //  in a single declaration now.
        //$keywords = explode(' ', $spreadsheet->getProperties()->getKeywords());
        //foreach ($keywords as $keyword) {
        //    $objWriter->writeElement('meta:keyword', $keyword);
        //}

        //<meta:document-statistic meta:table-count="XXX" meta:cell-count="XXX" meta:object-count="XXX"/>
        $objWriter->startElement('meta:user-defined');
        $objWriter->writeAttribute('meta:name', 'Company');
        $objWriter->writeRawData($spreadsheet->getProperties()->getCompany());
        $objWriter->endElement();

        $objWriter->startElement('meta:user-defined');
        $objWriter->writeAttribute('meta:name', 'category');
        $objWriter->writeRawData($spreadsheet->getProperties()->getCategory());
        $objWriter->endElement();

        self::writeDocPropsCustom($objWriter, $spreadsheet);

        $objWriter->endElement();

        $objWriter->endElement();

        return $objWriter->getData();
    }

    private static function writeDocPropsCustom(XMLWriter $objWriter, Spreadsheet $spreadsheet): void
    {
        $customPropertyList = $spreadsheet->getProperties()->getCustomProperties();
        foreach ($customPropertyList as $key => $customProperty) {
            $propertyValue = $spreadsheet->getProperties()->getCustomPropertyValue($customProperty);
            $propertyType = $spreadsheet->getProperties()->getCustomPropertyType($customProperty);

            $objWriter->startElement('meta:user-defined');
            $objWriter->writeAttribute('meta:name', $customProperty);

            switch ($propertyType) {
                case Properties::PROPERTY_TYPE_INTEGER:
                case Properties::PROPERTY_TYPE_FLOAT:
                    $objWriter->writeAttribute('meta:value-type', 'float');
                    $objWriter->writeRawData($propertyValue);

                    break;
                case Properties::PROPERTY_TYPE_BOOLEAN:
                    $objWriter->writeAttribute('meta:value-type', 'boolean');
                    $objWriter->writeRawData($propertyValue ? 'true' : 'false');

                    break;
                case Properties::PROPERTY_TYPE_DATE:
                    $objWriter->writeAttribute('meta:value-type', 'date');
                    $dtobj = Date::dateTimeFromTimestamp($propertyValue ?? 0);
                    $objWriter->writeRawData($dtobj->format(DATE_W3C));

                    break;
                default:
                    $objWriter->writeRawData($propertyValue);

                    break;
            }

            $objWriter->endElement();
        }
    }
}
