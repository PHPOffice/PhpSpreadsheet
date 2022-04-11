<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Settings;
use SimpleXMLElement;

class XmlParser
{
    /**
     * @param string[] $values
     *
     * @return SimpleXMLElement[]
     */
    public static function loadXmlArray(XmlScanner $securityScanner, array $values, string $ns = ''): array
    {
        $xmlArray = [];
        foreach ($values as $stringXml) {
            $xmlArray[] = self::loadXml($securityScanner, $stringXml, $ns);
        }

        return $xmlArray;
    }

    public static function loadXml(XmlScanner $securityScanner, string $contents, string $ns = ''): SimpleXMLElement
    {
        $rels = simplexml_load_string(
            $securityScanner->scan($contents),
            SimpleXMLElement::class,
            Settings::getLibXmlLoaderOptions(),
            $ns
        );

        return Xlsx::testSimpleXml($rels);
    }
}
