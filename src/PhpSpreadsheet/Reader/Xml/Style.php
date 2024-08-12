<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Style\Protection;
use SimpleXMLElement;

class Style
{
    /**
     * Formats.
     */
    protected array $styles = [];

    public function parseStyles(SimpleXMLElement $xml, array $namespaces): array
    {
        $children = $xml->children('urn:schemas-microsoft-com:office:spreadsheet');
        $stylesXml = $children->Styles[0];
        if (!isset($stylesXml) || !is_iterable($stylesXml)) {
            return [];
        }

        $alignmentStyleParser = new Style\Alignment();
        $borderStyleParser = new Style\Border();
        $fontStyleParser = new Style\Font();
        $fillStyleParser = new Style\Fill();
        $numberFormatStyleParser = new Style\NumberFormat();

        foreach ($stylesXml as $style) {
            $style_ss = self::getAttributes($style, $namespaces['ss']);
            $styleID = (string) $style_ss['ID'];
            $this->styles[$styleID] = $this->styles['Default'] ?? [];

            $alignment = $border = $font = $fill = $numberFormat = $protection = [];

            foreach ($style as $styleType => $styleDatax) {
                $styleData = self::getSxml($styleDatax);
                $styleAttributes = $styleData->attributes($namespaces['ss']);

                switch ($styleType) {
                    case 'Alignment':
                        if ($styleAttributes) {
                            $alignment = $alignmentStyleParser->parseStyle($styleAttributes);
                        }

                        break;
                    case 'Borders':
                        $border = $borderStyleParser->parseStyle($styleData, $namespaces);

                        break;
                    case 'Font':
                        if ($styleAttributes) {
                            $font = $fontStyleParser->parseStyle($styleAttributes);
                        }

                        break;
                    case 'Interior':
                        if ($styleAttributes) {
                            $fill = $fillStyleParser->parseStyle($styleAttributes);
                        }

                        break;
                    case 'NumberFormat':
                        if ($styleAttributes) {
                            $numberFormat = $numberFormatStyleParser->parseStyle($styleAttributes);
                        }

                        break;
                    case 'Protection':
                        $locked = $hidden = null;
                        $styleAttributesP = $styleData->attributes($namespaces['x']);
                        if (isset($styleAttributes['Protected'])) {
                            $locked = ((bool) (string) $styleAttributes['Protected']) ? Protection::PROTECTION_PROTECTED : Protection::PROTECTION_UNPROTECTED;
                        }
                        if (isset($styleAttributesP['HideFormula'])) {
                            $hidden = ((bool) (string) $styleAttributesP['HideFormula']) ? Protection::PROTECTION_PROTECTED : Protection::PROTECTION_UNPROTECTED;
                        }
                        if ($locked !== null || $hidden !== null) {
                            $protection['protection'] = [];
                            if ($locked !== null) {
                                $protection['protection']['locked'] = $locked;
                            }
                            if ($hidden !== null) {
                                $protection['protection']['hidden'] = $hidden;
                            }
                        }

                        break;
                }
            }

            $this->styles[$styleID] = array_merge($alignment, $border, $font, $fill, $numberFormat, $protection);
        }

        return $this->styles;
    }

    private static function getAttributes(?SimpleXMLElement $simple, string $node): SimpleXMLElement
    {
        return ($simple === null) ? new SimpleXMLElement('<xml></xml>') : ($simple->attributes($node) ?? new SimpleXMLElement('<xml></xml>'));
    }

    private static function getSxml(?SimpleXMLElement $simple): SimpleXMLElement
    {
        return ($simple !== null) ? $simple : new SimpleXMLElement('<xml></xml>');
    }
}
