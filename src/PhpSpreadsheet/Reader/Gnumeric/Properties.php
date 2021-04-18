<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Gnumeric;

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
                    $creationDate = strtotime($propertyValue);
                    $creationDate = $creationDate === false ? time() : $creationDate;
                    $docProps->setCreated($creationDate);
                    $docProps->setModified($creationDate);

                    break;
                case 'description':
                    $docProps->setDescription($propertyValue);

                    break;
            }
        }
    }

    private function docPropertiesMeta(SimpleXMLElement $officePropertyMeta, array $namespacesMeta): void
    {
        $docProps = $this->spreadsheet->getProperties();
        foreach ($officePropertyMeta as $propertyName => $propertyValue) {
            if ($propertyValue === null) {
                continue;
            }

            $attributes = $propertyValue->attributes($namespacesMeta['meta']);
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
                    $creationDate = strtotime($propertyValue);
                    $creationDate = $creationDate === false ? time() : $creationDate;
                    $docProps->setCreated($creationDate);
                    $docProps->setModified($creationDate);

                    break;
                case 'user-defined':
                    [, $attrName] = explode(':', $attributes['name']);
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

                    break;
            }
        }
    }

    public function readProperties(SimpleXMLElement $xml, SimpleXMLElement $gnmXML, array $namespacesMeta): void
    {
        if (isset($namespacesMeta['office'])) {
            $officeXML = $xml->children($namespacesMeta['office']);
            $officeDocXML = $officeXML->{'document-meta'};
            $officeDocMetaXML = $officeDocXML->meta;

            foreach ($officeDocMetaXML as $officePropertyData) {
                $officePropertyDC = [];
                if (isset($namespacesMeta['dc'])) {
                    $officePropertyDC = $officePropertyData->children($namespacesMeta['dc']);
                }
                $this->docPropertiesDC($officePropertyDC);

                $officePropertyMeta = [];
                if (isset($namespacesMeta['meta'])) {
                    $officePropertyMeta = $officePropertyData->children($namespacesMeta['meta']);
                }
                $this->docPropertiesMeta($officePropertyMeta, $namespacesMeta);
            }
        } elseif (isset($gnmXML->Summary)) {
            $this->docPropertiesOld($gnmXML);
        }
    }
}
