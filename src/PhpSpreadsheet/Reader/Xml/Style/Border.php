<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xml\Style;

use PhpOffice\PhpSpreadsheet\Style\Border as BorderStyle;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use SimpleXMLElement;

class Border extends StyleBase
{
    protected const BORDER_POSITIONS = [
        'top',
        'left',
        'bottom',
        'right',
    ];

    public const BORDER_MAPPINGS = [
        'borderStyle' => [
            'continuous' => BorderStyle::BORDER_HAIR,
            'dash' => BorderStyle::BORDER_DASHED,
            'dashdot' => BorderStyle::BORDER_DASHDOT,
            'dashdotdot' => BorderStyle::BORDER_DASHDOTDOT,
            'dot' => BorderStyle::BORDER_DOTTED,
            'double' => BorderStyle::BORDER_DOUBLE,
            '0continuous' => BorderStyle::BORDER_HAIR,
            '0dash' => BorderStyle::BORDER_DASHED,
            '0dashdot' => BorderStyle::BORDER_DASHDOT,
            '0dashdotdot' => BorderStyle::BORDER_DASHDOTDOT,
            '0dot' => BorderStyle::BORDER_DOTTED,
            '0double' => BorderStyle::BORDER_DOUBLE,
            '1continuous' => BorderStyle::BORDER_THIN,
            '1dash' => BorderStyle::BORDER_DASHED,
            '1dashdot' => BorderStyle::BORDER_DASHDOT,
            '1dashdotdot' => BorderStyle::BORDER_DASHDOTDOT,
            '1dot' => BorderStyle::BORDER_DOTTED,
            '1double' => BorderStyle::BORDER_DOUBLE,
            '2continuous' => BorderStyle::BORDER_MEDIUM,
            '2dash' => BorderStyle::BORDER_MEDIUMDASHED,
            '2dashdot' => BorderStyle::BORDER_MEDIUMDASHDOT,
            '2dashdotdot' => BorderStyle::BORDER_MEDIUMDASHDOTDOT,
            '2dot' => BorderStyle::BORDER_DOTTED,
            '2double' => BorderStyle::BORDER_DOUBLE,
            '3continuous' => BorderStyle::BORDER_THICK,
            '3dash' => BorderStyle::BORDER_MEDIUMDASHED,
            '3dashdot' => BorderStyle::BORDER_MEDIUMDASHDOT,
            '3dashdotdot' => BorderStyle::BORDER_MEDIUMDASHDOTDOT,
            '3dot' => BorderStyle::BORDER_DOTTED,
            '3double' => BorderStyle::BORDER_DOUBLE,
        ],
    ];

    /**
     * @param string[] $namespaces
     *
     * @return mixed[]
     */
    public function parseStyle(SimpleXMLElement $styleData, array $namespaces): array
    {
        $style = [];

        $diagonalDirection = '';
        $borderPosition = '';
        foreach ($styleData->Border as $borderStyle) {
            $borderAttributes = self::getAttributes($borderStyle, $namespaces['ss']);
            $thisBorder = [];
            $styleType = (string) $borderAttributes->Weight;
            $styleType .= strtolower((string) $borderAttributes->LineStyle);
            $thisBorder['borderStyle'] = self::BORDER_MAPPINGS['borderStyle'][$styleType] ?? BorderStyle::BORDER_NONE;

            foreach ($borderAttributes as $borderStyleKey => $borderStyleValuex) {
                $borderStyleValue = (string) $borderStyleValuex;
                switch ($borderStyleKey) {
                    case 'Position':
                        /** @var string $diagonalDirection */
                        [$borderPosition, $diagonalDirection]
                            = $this->parsePosition($borderStyleValue, $diagonalDirection);

                        break;
                    case 'Color':
                        $borderColour = substr($borderStyleValue, 1);
                        $thisBorder['color']['rgb'] = $borderColour;

                        break;
                }
            }

            if ($borderPosition) {
                $style['borders'][$borderPosition] = $thisBorder;
            } elseif ($diagonalDirection) {
                $style['borders']['diagonalDirection'] = $diagonalDirection;
                $style['borders']['diagonal'] = $thisBorder;
            }
        }

        return $style;
    }

    /** @return mixed[] */
    protected function parsePosition(string $borderStyleValue, string $diagonalDirection): array
    {
        $borderStyleValue = strtolower($borderStyleValue);

        if (in_array($borderStyleValue, self::BORDER_POSITIONS)) {
            $borderPosition = $borderStyleValue;
        } elseif ($borderStyleValue === 'diagonalleft') {
            $diagonalDirection = $diagonalDirection ? Borders::DIAGONAL_BOTH : Borders::DIAGONAL_DOWN;
        } elseif ($borderStyleValue === 'diagonalright') {
            $diagonalDirection = $diagonalDirection ? Borders::DIAGONAL_BOTH : Borders::DIAGONAL_UP;
        }

        return [$borderPosition ?? null, $diagonalDirection];
    }
}
