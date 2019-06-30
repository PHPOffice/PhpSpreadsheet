<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Document\Properties as DocumentProperties;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Properties
{
    private $spreadsheet;

    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    public function load(\SimpleXMLElement $xml, $namespacesMeta)
    {
        $docProps = $this->spreadsheet->getProperties();
        $officeProperty = $xml->children($namespacesMeta['office']);
        foreach ($officeProperty as $officePropertyData) {
            /** @var \SimpleXMLElement $officePropertyData */
            $officePropertiesDC = [];
            if (isset($namespacesMeta['dc'])) {
                $officePropertiesDC = $officePropertyData->children($namespacesMeta['dc']);
            }
            $this->setCoreProperties($docProps, $officePropertiesDC);

            $officePropertyMeta = [];
            if (isset($namespacesMeta['dc'])) {
                $officePropertyMeta = $officePropertyData->children($namespacesMeta['meta']);
            }
            foreach ($officePropertyMeta as $propertyName => $propertyValue) {
                $this->setMetaProperties($namespacesMeta, $propertyValue, $propertyName, $docProps);
            }
        }
    }

    private function setCoreProperties(DocumentProperties $docProps, \SimpleXMLElement $officePropertyDC)
    {
        foreach ($officePropertyDC as $propertyName => $propertyValue) {
            $propertyValue = (string) $propertyValue;
            switch ($propertyName) {
                case 'title':
                    $docProps->setTitle($propertyValue);

                    break;
                case 'subject':
                    $docProps->setSubject($propertyValue);

                    break;
                case 'creator':
                    $docProps->setCreator($propertyValue);
                    $docProps->setLastModifiedBy($propertyValue);

                    break;
                case 'creation-date':
                    $creationDate = strtotime($propertyValue);
                    $docProps->setCreated($creationDate);
                    $docProps->setModified($creationDate);

                    break;
                case 'keyword':
                    $docProps->setKeywords($propertyValue);

                    break;
                case 'description':
                    $docProps->setDescription($propertyValue);

                    break;
            }
        }
    }

    private function setMetaProperties(
        $namespacesMeta,
        \SimpleXMLElement $propertyValue,
        $propertyName,
        DocumentProperties $docProps
    ) {
        $propertyValueAttributes = $propertyValue->attributes($namespacesMeta['meta']);
        $propertyValue = (string) $propertyValue;
        switch ($propertyName) {
            case 'initial-creator':
                $docProps->setCreator($propertyValue);

                break;
            case 'keyword':
                $docProps->setKeywords($propertyValue);

                break;
            case 'creation-date':
                $creationDate = strtotime($propertyValue);
                $docProps->setCreated($creationDate);

                break;
            case 'user-defined':
                $this->setUserDefinedProperty($propertyValueAttributes, $propertyValue, $docProps);

                break;
        }
    }

    private function setUserDefinedProperty($propertyValueAttributes, $propertyValue, DocumentProperties $docProps)
    {
        $propertyValueName = '';
        $propertyValueType = DocumentProperties::PROPERTY_TYPE_STRING;
        foreach ($propertyValueAttributes as $key => $value) {
            if ($key == 'name') {
                $propertyValueName = (string) $value;
            } elseif ($key == 'value-type') {
                switch ($value) {
                    case 'date':
                        $propertyValue = DocumentProperties::convertProperty($propertyValue, 'date');
                        $propertyValueType = DocumentProperties::PROPERTY_TYPE_DATE;

                        break;
                    case 'boolean':
                        $propertyValue = DocumentProperties::convertProperty($propertyValue, 'bool');
                        $propertyValueType = DocumentProperties::PROPERTY_TYPE_BOOLEAN;

                        break;
                    case 'float':
                        $propertyValue = DocumentProperties::convertProperty($propertyValue, 'r4');
                        $propertyValueType = DocumentProperties::PROPERTY_TYPE_FLOAT;

                        break;
                    default:
                        $propertyValueType = DocumentProperties::PROPERTY_TYPE_STRING;
                }
            }
        }

        $docProps->setCustomProperty($propertyValueName, $propertyValue, $propertyValueType);
    }
}
