<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xml\Style;

use PhpOffice\PhpSpreadsheet\Style\Alignment as AlignmentStyles;
use SimpleXMLElement;

class Alignment extends StyleBase
{
    protected const VERTICAL_ALIGNMENT_STYLES = [
        AlignmentStyles::VERTICAL_BOTTOM,
        AlignmentStyles::VERTICAL_TOP,
        AlignmentStyles::VERTICAL_CENTER,
        AlignmentStyles::VERTICAL_JUSTIFY,
    ];

    protected const HORIZONTAL_ALIGNMENT_STYLES = [
        AlignmentStyles::HORIZONTAL_GENERAL,
        AlignmentStyles::HORIZONTAL_LEFT,
        AlignmentStyles::HORIZONTAL_RIGHT,
        AlignmentStyles::HORIZONTAL_CENTER,
        AlignmentStyles::HORIZONTAL_CENTER_CONTINUOUS,
        AlignmentStyles::HORIZONTAL_JUSTIFY,
    ];

    /** @return mixed[] */
    public function parseStyle(SimpleXMLElement $styleAttributes): array
    {
        $style = [];

        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
            $styleAttributeValue = (string) $styleAttributeValue;
            switch ($styleAttributeKey) {
                case 'Vertical':
                    if (self::identifyFixedStyleValue(self::VERTICAL_ALIGNMENT_STYLES, $styleAttributeValue)) {
                        $style['alignment']['vertical'] = $styleAttributeValue;
                    }

                    break;
                case 'Horizontal':
                    if (self::identifyFixedStyleValue(self::HORIZONTAL_ALIGNMENT_STYLES, $styleAttributeValue)) {
                        $style['alignment']['horizontal'] = $styleAttributeValue;
                    }

                    break;
                case 'WrapText':
                    $style['alignment']['wrapText'] = true;

                    break;
                case 'Rotate':
                    $style['alignment']['textRotation'] = $styleAttributeValue;

                    break;
                case 'Indent':
                    $style['alignment']['indent'] = $styleAttributeValue;

                    break;
            }
        }

        return $style;
    }
}
