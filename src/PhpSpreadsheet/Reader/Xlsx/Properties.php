<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Document\Properties as DocumentProperties;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use SimpleXMLElement;

class Properties
{
    private XmlScanner $securityScanner;

    private DocumentProperties $docProps;

    public function __construct(XmlScanner $securityScanner, DocumentProperties $docProps)
    {
        $this->securityScanner = $securityScanner;
        $this->docProps = $docProps;
    }

    private function extractPropertyData(string $propertyData): ?SimpleXMLElement
    {
        // okay to omit namespace because everything will be processed by xpath
        $obj = simplexml_load_string(
            $this->securityScanner->scan($propertyData)
        );

        return $obj === false ? null : $obj;
    }

    public function readCoreProperties(string $propertyData): void
    {
        $xmlCore = $this->extractPropertyData($propertyData);

        if (is_object($xmlCore)) {
            $xmlCore->registerXPathNamespace('dc', Namespaces::DC_ELEMENTS);
            $xmlCore->registerXPathNamespace('dcterms', Namespaces::DC_TERMS);
            $xmlCore->registerXPathNamespace('cp', Namespaces::CORE_PROPERTIES2);

            $this->docProps->setCreator($this->getArrayItem($xmlCore->xpath('dc:creator')));
            $this->docProps->setLastModifiedBy($this->getArrayItem($xmlCore->xpath('cp:lastModifiedBy')));
            $this->docProps->setCreated($this->getArrayItem($xmlCore->xpath('dcterms:created'))); //! respect xsi:type
            $this->docProps->setModified($this->getArrayItem($xmlCore->xpath('dcterms:modified'))); //! respect xsi:type
            $this->docProps->setTitle($this->getArrayItem($xmlCore->xpath('dc:title')));
            $this->docProps->setDescription($this->getArrayItem($xmlCore->xpath('dc:description')));
            $this->docProps->setSubject($this->getArrayItem($xmlCore->xpath('dc:subject')));
            $this->docProps->setKeywords($this->getArrayItem($xmlCore->xpath('cp:keywords')));
            $this->docProps->setCategory($this->getArrayItem($xmlCore->xpath('cp:category')));
        }
    }

    public function readExtendedProperties(string $propertyData): void
    {
        $xmlCore = $this->extractPropertyData($propertyData);

        if (is_object($xmlCore)) {
            if (isset($xmlCore->Company)) {
                $this->docProps->setCompany((string) $xmlCore->Company);
            }
            if (isset($xmlCore->Manager)) {
                $this->docProps->setManager((string) $xmlCore->Manager);
            }
            if (isset($xmlCore->HyperlinkBase)) {
                $this->docProps->setHyperlinkBase((string) $xmlCore->HyperlinkBase);
            }
        }
    }

    public function readCustomProperties(string $propertyData): void
    {
        $xmlCore = $this->extractPropertyData($propertyData);

        if (is_object($xmlCore)) {
            foreach ($xmlCore as $xmlProperty) {
                /** @var SimpleXMLElement $xmlProperty */
                $cellDataOfficeAttributes = $xmlProperty->attributes();
                if (isset($cellDataOfficeAttributes['name'])) {
                    $propertyName = (string) $cellDataOfficeAttributes['name'];
                    $cellDataOfficeChildren = $xmlProperty->children('http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes');

                    $attributeType = $cellDataOfficeChildren->getName();
                    /** @var SimpleXMLElement */
                    $attributeValue = $cellDataOfficeChildren->{$attributeType};
                    $attributeValue = (string) $attributeValue;
                    $attributeValue = DocumentProperties::convertProperty($attributeValue, $attributeType);
                    $attributeType = DocumentProperties::convertPropertyType($attributeType);
                    $this->docProps->setCustomProperty($propertyName, $attributeValue, $attributeType);
                }
            }
        }
    }

    /** @param null|false|scalar[] $array */
    private function getArrayItem(null|array|false $array): string
    {
        return is_array($array) ? (string) ($array[0] ?? '') : '';
    }
}
