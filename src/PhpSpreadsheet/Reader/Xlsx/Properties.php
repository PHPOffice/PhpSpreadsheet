<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Document\Properties as DocumentProperties;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Settings;
use SimpleXMLElement;

class Properties
{
    /** @var XmlScanner */
    private $securityScanner;

    /** @var DocumentProperties */
    private $docProps;

    public function __construct(XmlScanner $securityScanner, DocumentProperties $docProps)
    {
        $this->securityScanner = $securityScanner;
        $this->docProps = $docProps;
    }

    /**
     * @param mixed $obj
     */
    private static function nullOrSimple($obj): ?SimpleXMLElement
    {
        return ($obj instanceof SimpleXMLElement) ? $obj : null;
    }

    private function extractPropertyData(string $propertyData): ?SimpleXMLElement
    {
        // okay to omit namespace because everything will be processed by xpath
        $obj = simplexml_load_string(
            $this->securityScanner->scan($propertyData),
            'SimpleXMLElement',
            Settings::getLibXmlLoaderOptions()
        );

        return self::nullOrSimple($obj);
    }

    public function readCoreProperties(string $propertyData): void
    {
        $xmlCore = $this->extractPropertyData($propertyData);

        if (is_object($xmlCore)) {
            $xmlCore->registerXPathNamespace('dc', Namespaces::DC_ELEMENTS);
            $xmlCore->registerXPathNamespace('dcterms', Namespaces::DC_TERMS);
            $xmlCore->registerXPathNamespace('cp', Namespaces::CORE_PROPERTIES2);

            $this->docProps->setCreator((string) self::getArrayItem($xmlCore->xpath('dc:creator')));
            $this->docProps->setLastModifiedBy((string) self::getArrayItem($xmlCore->xpath('cp:lastModifiedBy')));
            $this->docProps->setCreated((int) strtotime((string) self::getArrayItem($xmlCore->xpath('dcterms:created')))); //! respect xsi:type
            $this->docProps->setModified((int) strtotime((string) self::getArrayItem($xmlCore->xpath('dcterms:modified')))); //! respect xsi:type
            $this->docProps->setTitle((string) self::getArrayItem($xmlCore->xpath('dc:title')));
            $this->docProps->setDescription((string) self::getArrayItem($xmlCore->xpath('dc:description')));
            $this->docProps->setSubject((string) self::getArrayItem($xmlCore->xpath('dc:subject')));
            $this->docProps->setKeywords((string) self::getArrayItem($xmlCore->xpath('cp:keywords')));
            $this->docProps->setCategory((string) self::getArrayItem($xmlCore->xpath('cp:category')));
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
                    $attributeValue = (string) $cellDataOfficeChildren->{$attributeType};
                    $attributeValue = DocumentProperties::convertProperty($attributeValue, $attributeType);
                    $attributeType = DocumentProperties::convertPropertyType($attributeType);
                    $this->docProps->setCustomProperty($propertyName, $attributeValue, $attributeType);
                }
            }
        }
    }

    /**
     * @param array|false $array
     * @param mixed $key
     */
    private static function getArrayItem($array, $key = 0): ?SimpleXMLElement
    {
        return is_array($array) ? ($array[$key] ?? null) : null;
    }
}
