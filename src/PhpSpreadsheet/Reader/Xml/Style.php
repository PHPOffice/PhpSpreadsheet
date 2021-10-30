<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xml;

use SimpleXMLElement;

class Style
{
    /**
     * Formats.
     *
     * @var array
     */
    protected $styles = [];

    public function parseStyles(SimpleXMLElement $xml, array $namespaces): array
    {
        if (!isset($xml->Styles)) {
            return [];
        }

        $alignmentStyleParser = new Style\Alignment();
        $borderStyleParser = new Style\Border();
        $fontStyleParser = new Style\Font();
        $fillStyleParser = new Style\Fill();
        $numberFormatStyleParser = new Style\NumberFormat();

        foreach ($xml->Styles[0] as $style) {
            $style_ss = self::getAttributes($style, $namespaces['ss']);
            $styleID = (string) $style_ss['ID'];
            $this->styles[$styleID] = $this->styles['Default'] ?? [];

            $alignment = $border = $font = $fill = $numberFormat = [];

            foreach ($style as $styleType => $styleDatax) {
                $styleData = $styleDatax ?? new SimpleXMLElement('<xml></xml>');
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
                }
            }

            $this->styles[$styleID] = array_merge($alignment, $border, $font, $fill, $numberFormat);
        }

        return $this->styles;
    }

    protected static function getAttributes(?SimpleXMLElement $simple, string $node): SimpleXMLElement
    {
        return ($simple === null)
            ? new SimpleXMLElement('<xml></xml>')
            : ($simple->attributes($node) ?? new SimpleXMLElement('<xml></xml>'));
    }
}
