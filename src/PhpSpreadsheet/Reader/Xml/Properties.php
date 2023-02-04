<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Document\Properties as DocumentProperties;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use SimpleXMLElement;

class Properties
{
    /**
     * @var Spreadsheet
     */
    protected $spreadsheet;

    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    public function readProperties(SimpleXMLElement $xml, array $namespaces): void
    {
        $this->readStandardProperties($xml);
        $this->readCustomProperties($xml, $namespaces);
    }

    protected function readStandardProperties(SimpleXMLElement $xml): void
    {
        if (isset($xml->DocumentProperties[0])) {
            $docProps = $this->spreadsheet->getProperties();

            foreach ($xml->DocumentProperties[0] as $propertyName => $propertyValue) {
                $propertyValue = (string) $propertyValue;

                $this->processStandardProperty($docProps, $propertyName, $propertyValue);
            }
        }
    }

    protected function readCustomProperties(SimpleXMLElement $xml, array $namespaces): void
    {
        if (isset($xml->CustomDocumentProperties) && is_iterable($xml->CustomDocumentProperties[0])) {
            $docProps = $this->spreadsheet->getProperties();

            foreach ($xml->CustomDocumentProperties[0] as $propertyName => $propertyValue) {
                $propertyAttributes = self::getAttributes($propertyValue, $namespaces['dt']);
                $propertyName = (string) preg_replace_callback('/_x([0-9a-f]{4})_/i', [$this, 'hex2str'], $propertyName);

                $this->processCustomProperty($docProps, $propertyName, $propertyValue, $propertyAttributes);
            }
        }
    }

    protected function processStandardProperty(
        DocumentProperties $docProps,
        string $propertyName,
        string $stringValue
    ): void {
        switch ($propertyName) {
            case 'Title':
                $docProps->setTitle($stringValue);

                break;
            case 'Subject':
                $docProps->setSubject($stringValue);

                break;
            case 'Author':
                $docProps->setCreator($stringValue);

                break;
            case 'Created':
                $docProps->setCreated($stringValue);

                break;
            case 'LastAuthor':
                $docProps->setLastModifiedBy($stringValue);

                break;
            case 'LastSaved':
                $docProps->setModified($stringValue);

                break;
            case 'Company':
                $docProps->setCompany($stringValue);

                break;
            case 'Category':
                $docProps->setCategory($stringValue);

                break;
            case 'Manager':
                $docProps->setManager($stringValue);

                break;
            case 'Keywords':
                $docProps->setKeywords($stringValue);

                break;
            case 'Description':
                $docProps->setDescription($stringValue);

                break;
        }
    }

    protected function processCustomProperty(
        DocumentProperties $docProps,
        string $propertyName,
        ?SimpleXMLElement $propertyValue,
        SimpleXMLElement $propertyAttributes
    ): void {
        $propertyType = DocumentProperties::PROPERTY_TYPE_UNKNOWN;

        switch ((string) $propertyAttributes) {
            case 'string':
                $propertyType = DocumentProperties::PROPERTY_TYPE_STRING;
                $propertyValue = trim((string) $propertyValue);

                break;
            case 'boolean':
                $propertyType = DocumentProperties::PROPERTY_TYPE_BOOLEAN;
                $propertyValue = (bool) $propertyValue;

                break;
            case 'integer':
                $propertyType = DocumentProperties::PROPERTY_TYPE_INTEGER;
                $propertyValue = (int) $propertyValue;

                break;
            case 'float':
                $propertyType = DocumentProperties::PROPERTY_TYPE_FLOAT;
                $propertyValue = (float) $propertyValue;

                break;
            case 'dateTime.tz':
                $propertyType = DocumentProperties::PROPERTY_TYPE_DATE;
                $propertyValue = trim((string) $propertyValue);

                break;
        }

        $docProps->setCustomProperty($propertyName, $propertyValue, $propertyType);
    }

    protected function hex2str(array $hex): string
    {
        return mb_chr((int) hexdec($hex[1]), 'UTF-8');
    }

    private static function getAttributes(?SimpleXMLElement $simple, string $node): SimpleXMLElement
    {
        return ($simple === null) ? new SimpleXMLElement('<xml></xml>') : ($simple->attributes($node) ?? new SimpleXMLElement('<xml></xml>'));
    }
}
