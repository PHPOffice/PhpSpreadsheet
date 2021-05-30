<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
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

    private function docPropertiesOld(SimpleXMLElement $gnmXML): void
    {
        $docProps = $this->spreadsheet->getProperties();
        foreach ($gnmXML->Summary->Item as $summaryItem) {
            $propertyName = $summaryItem->name;
            $propertyValue = $summaryItem->{'val-string'};
            switch ($propertyName) {
                case 'title':
                    $docProps->setTitle(trim($propertyValue));

                    break;
                case 'comments':
                    $docProps->setDescription(trim($propertyValue));

                    break;
                case 'keywords':
                    $docProps->setKeywords(trim($propertyValue));

                    break;
                case 'category':
                    $docProps->setCategory(trim($propertyValue));

                    break;
                case 'manager':
                    $docProps->setManager(trim($propertyValue));

                    break;
                case 'author':
                    $docProps->setCreator(trim($propertyValue));
                    $docProps->setLastModifiedBy(trim($propertyValue));

                    break;
                case 'company':
                    $docProps->setCompany(trim($propertyValue));

                    break;
            }
        }
    }

    private function docPropertiesDC(SimpleXMLElement $officePropertyDC): void
    {
        $docProps = $this->spreadsheet->getProperties();
        foreach ($officePropertyDC as $propertyName => $propertyValue) {
            $propertyValue = trim((string) $propertyValue);
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
                case 'date':
                    $creationDate = $propertyValue;
                    $docProps->setModified($creationDate);

                    break;
                case 'description':
                    $docProps->setDescription($propertyValue);

                    break;
            }
        }
    }

    private function docPropertiesMeta(SimpleXMLElement $officePropertyMeta): void
    {
        $docProps = $this->spreadsheet->getProperties();
        foreach ($officePropertyMeta as $propertyName => $propertyValue) {
            if ($propertyValue !== null) {
                $attributes = $propertyValue->attributes(Gnumeric::NAMESPACE_META);
                $propertyValue = trim((string) $propertyValue);
                switch ($propertyName) {
                    case 'keyword':
                        $docProps->setKeywords($propertyValue);

                        break;
                    case 'initial-creator':
                        $docProps->setCreator($propertyValue);
                        $docProps->setLastModifiedBy($propertyValue);

                        break;
                    case 'creation-date':
                        $creationDate = $propertyValue;
                        $docProps->setCreated($creationDate);

                        break;
                    case 'user-defined':
                        [, $attrName] = explode(':', $attributes['name']);
                        $this->userDefinedProperties($attrName, $propertyValue);

                        break;
                }
            }
        }
    }

    private function userDefinedProperties(string $attrName, string $propertyValue): void
    {
        $docProps = $this->spreadsheet->getProperties();
        switch ($attrName) {
            case 'publisher':
                $docProps->setCompany($propertyValue);

                break;
            case 'category':
                $docProps->setCategory($propertyValue);

                break;
            case 'manager':
                $docProps->setManager($propertyValue);

                break;
        }
    }

    public function readProperties(SimpleXMLElement $xml, SimpleXMLElement $gnmXML): void
    {
        $officeXML = $xml->children(Gnumeric::NAMESPACE_OFFICE);
        if (!empty($officeXML)) {
            $officeDocXML = $officeXML->{'document-meta'};
            $officeDocMetaXML = $officeDocXML->meta;

            foreach ($officeDocMetaXML as $officePropertyData) {
                $officePropertyDC = $officePropertyData->children(Gnumeric::NAMESPACE_DC);
                $this->docPropertiesDC($officePropertyDC);

                $officePropertyMeta = $officePropertyData->children(Gnumeric::NAMESPACE_META);
                $this->docPropertiesMeta($officePropertyMeta);
            }
        } elseif (isset($gnmXML->Summary)) {
            $this->docPropertiesOld($gnmXML);
        }
    }
}
