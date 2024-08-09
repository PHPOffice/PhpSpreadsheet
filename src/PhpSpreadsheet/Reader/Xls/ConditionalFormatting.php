<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Style\FillPattern;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;

class ConditionalFormatting extends Xls
{
    /**
     * @var array<int, string>
     */
    private static array $types = [
        0x01 => Conditional::CONDITION_CELLIS,
        0x02 => Conditional::CONDITION_EXPRESSION,
    ];

    /**
     * @var array<int, string>
     */
    private static array $operators = [
        0x00 => Conditional::OPERATOR_NONE,
        0x01 => Conditional::OPERATOR_BETWEEN,
        0x02 => Conditional::OPERATOR_NOTBETWEEN,
        0x03 => Conditional::OPERATOR_EQUAL,
        0x04 => Conditional::OPERATOR_NOTEQUAL,
        0x05 => Conditional::OPERATOR_GREATERTHAN,
        0x06 => Conditional::OPERATOR_LESSTHAN,
        0x07 => Conditional::OPERATOR_GREATERTHANOREQUAL,
        0x08 => Conditional::OPERATOR_LESSTHANOREQUAL,
    ];

    public static function type(int $type): ?string
    {
        if (isset(self::$types[$type])) {
            return self::$types[$type];
        }

        return null;
    }

    public static function operator(int $operator): ?string
    {
        if (isset(self::$operators[$operator])) {
            return self::$operators[$operator];
        }

        return null;
    }

    /**
     * Parse conditional formatting blocks.
     *
     * @see https://www.openoffice.org/sc/excelfileformat.pdf Search for CFHEADER followed by CFRULE
     */
    protected function readCFHeader2(Xls $xls): array
    {
        $length = self::getUInt2d($xls->data, $xls->pos + 2);
        $recordData = $xls->readRecordData($xls->data, $xls->pos + 4, $length);

        // move stream pointer forward to next record
        $xls->pos += 4 + $length;

        if ($xls->readDataOnly) {
            return [];
        }

        // offset: 0; size: 2; Rule Count
//        $ruleCount = self::getUInt2d($recordData, 0);

        // offset: var; size: var; cell range address list with
        $cellRangeAddressList = ($xls->version == self::XLS_BIFF8)
            ? Biff8::readBIFF8CellRangeAddressList(substr($recordData, 12))
            : Biff5::readBIFF5CellRangeAddressList(substr($recordData, 12));
        $cellRangeAddresses = $cellRangeAddressList['cellRangeAddresses'];

        return $cellRangeAddresses;
    }

    protected function readCFRule2(array $cellRangeAddresses, Xls $xls): void
    {
        $length = self::getUInt2d($xls->data, $xls->pos + 2);
        $recordData = $xls->readRecordData($xls->data, $xls->pos + 4, $length);

        // move stream pointer forward to next record
        $xls->pos += 4 + $length;

        if ($xls->readDataOnly) {
            return;
        }

        // offset: 0; size: 2; Options
        $cfRule = self::getUInt2d($recordData, 0);

        // bit: 8-15; mask: 0x00FF; type
        $type = (0x00FF & $cfRule) >> 0;
        $type = self::type($type);

        // bit: 0-7; mask: 0xFF00; type
        $operator = (0xFF00 & $cfRule) >> 8;
        $operator = self::operator($operator);

        if ($type === null || $operator === null) {
            return;
        }

        // offset: 2; size: 2; Size1
        $size1 = self::getUInt2d($recordData, 2);

        // offset: 4; size: 2; Size2
        $size2 = self::getUInt2d($recordData, 4);

        // offset: 6; size: 4; Options
        $options = self::getInt4d($recordData, 6);

        $style = new Style(false, true); // non-supervisor, conditional
        $noFormatSet = true;
        //$xls->getCFStyleOptions($options, $style);

        $hasFontRecord = (bool) ((0x04000000 & $options) >> 26);
        $hasAlignmentRecord = (bool) ((0x08000000 & $options) >> 27);
        $hasBorderRecord = (bool) ((0x10000000 & $options) >> 28);
        $hasFillRecord = (bool) ((0x20000000 & $options) >> 29);
        $hasProtectionRecord = (bool) ((0x40000000 & $options) >> 30);
        // note unexpected values for following 4
        $hasBorderLeft = !(bool) (0x00000400 & $options);
        $hasBorderRight = !(bool) (0x00000800 & $options);
        $hasBorderTop = !(bool) (0x00001000 & $options);
        $hasBorderBottom = !(bool) (0x00002000 & $options);

        $offset = 12;

        if ($hasFontRecord === true) {
            $fontStyle = substr($recordData, $offset, 118);
            $this->getCFFontStyle($fontStyle, $style, $xls);
            $offset += 118;
            $noFormatSet = false;
        }

        if ($hasAlignmentRecord === true) {
            //$alignmentStyle = substr($recordData, $offset, 8);
            //$this->getCFAlignmentStyle($alignmentStyle, $style, $xls);
            $offset += 8;
        }

        if ($hasBorderRecord === true) {
            $borderStyle = substr($recordData, $offset, 8);
            $this->getCFBorderStyle($borderStyle, $style, $hasBorderLeft, $hasBorderRight, $hasBorderTop, $hasBorderBottom, $xls);
            $offset += 8;
            $noFormatSet = false;
        }

        if ($hasFillRecord === true) {
            $fillStyle = substr($recordData, $offset, 4);
            $this->getCFFillStyle($fillStyle, $style, $xls);
            $offset += 4;
            $noFormatSet = false;
        }

        if ($hasProtectionRecord === true) {
            //$protectionStyle = substr($recordData, $offset, 4);
            //$this->getCFProtectionStyle($protectionStyle, $style, $xls);
            $offset += 2;
        }

        $formula1 = $formula2 = null;
        if ($size1 > 0) {
            $formula1 = $this->readCFFormula($recordData, $offset, $size1, $xls);
            if ($formula1 === null) {
                return;
            }

            $offset += $size1;
        }

        if ($size2 > 0) {
            $formula2 = $this->readCFFormula($recordData, $offset, $size2, $xls);
            if ($formula2 === null) {
                return;
            }

            $offset += $size2;
        }

        $this->setCFRules($cellRangeAddresses, $type, $operator, $formula1, $formula2, $style, $noFormatSet, $xls);
    }

    /*private function getCFStyleOptions(int $options, Style $style, Xls $xls): void
    {
    }*/

    private function getCFFontStyle(string $options, Style $style, Xls $xls): void
    {
        $fontSize = self::getInt4d($options, 64);
        if ($fontSize !== -1) {
            $style->getFont()->setSize($fontSize / 20); // Convert twips to points
        }
        $options68 = self::getInt4d($options, 68);
        $options88 = self::getInt4d($options, 88);

        if (($options88 & 2) === 0) {
            $bold = self::getUInt2d($options, 72); // 400 = normal, 700 = bold
            if ($bold !== 0) {
                $style->getFont()->setBold($bold >= 550);
            }
            if (($options68 & 2) !== 0) {
                $style->getFont()->setItalic(true);
            }
        }
        if (($options88 & 0x80) === 0) {
            if (($options68 & 0x80) !== 0) {
                $style->getFont()->setStrikethrough(true);
            }
        }

        $color = self::getInt4d($options, 80);

        if ($color !== -1) {
            $style->getFont()->getColor()->setRGB(Color::map($color, $xls->palette, $xls->version)['rgb']);
        }
    }

    /*private function getCFAlignmentStyle(string $options, Style $style, Xls $xls): void
    {
    }*/

    private function getCFBorderStyle(string $options, Style $style, bool $hasBorderLeft, bool $hasBorderRight, bool $hasBorderTop, bool $hasBorderBottom, Xls $xls): void
    {
        $valueArray = unpack('V', $options);
        $value = is_array($valueArray) ? $valueArray[1] : 0;
        $left = $value & 15;
        $right = ($value >> 4) & 15;
        $top = ($value >> 8) & 15;
        $bottom = ($value >> 12) & 15;
        $leftc = ($value >> 16) & 0x7F;
        $rightc = ($value >> 23) & 0x7F;
        $valueArray = unpack('V', substr($options, 4));
        $value = is_array($valueArray) ? $valueArray[1] : 0;
        $topc = $value & 0x7F;
        $bottomc = ($value & 0x3F80) >> 7;
        if ($hasBorderLeft) {
            $style->getBorders()->getLeft()
                ->setBorderStyle(self::BORDER_STYLE_MAP[$left]);
            $style->getBorders()->getLeft()->getColor()
                ->setRGB(Color::map($leftc, $xls->palette, $xls->version)['rgb']);
        }
        if ($hasBorderRight) {
            $style->getBorders()->getRight()
                ->setBorderStyle(self::BORDER_STYLE_MAP[$right]);
            $style->getBorders()->getRight()->getColor()
                ->setRGB(Color::map($rightc, $xls->palette, $xls->version)['rgb']);
        }
        if ($hasBorderTop) {
            $style->getBorders()->getTop()
                ->setBorderStyle(self::BORDER_STYLE_MAP[$top]);
            $style->getBorders()->getTop()->getColor()
                ->setRGB(Color::map($topc, $xls->palette, $xls->version)['rgb']);
        }
        if ($hasBorderBottom) {
            $style->getBorders()->getBottom()
                ->setBorderStyle(self::BORDER_STYLE_MAP[$bottom]);
            $style->getBorders()->getBottom()->getColor()
                ->setRGB(Color::map($bottomc, $xls->palette, $xls->version)['rgb']);
        }
    }

    private function getCFFillStyle(string $options, Style $style, Xls $xls): void
    {
        $fillPattern = self::getUInt2d($options, 0);
        // bit: 10-15; mask: 0xFC00; type
        $fillPattern = (0xFC00 & $fillPattern) >> 10;
        $fillPattern = FillPattern::lookup($fillPattern);
        $fillPattern = $fillPattern === Fill::FILL_NONE ? Fill::FILL_SOLID : $fillPattern;

        if ($fillPattern !== Fill::FILL_NONE) {
            $style->getFill()->setFillType($fillPattern);

            $fillColors = self::getUInt2d($options, 2);

            // bit: 0-6; mask: 0x007F; type
            $color1 = (0x007F & $fillColors) >> 0;

            // bit: 7-13; mask: 0x3F80; type
            $color2 = (0x3F80 & $fillColors) >> 7;
            if ($fillPattern === Fill::FILL_SOLID) {
                $style->getFill()->getStartColor()->setRGB(Color::map($color2, $xls->palette, $xls->version)['rgb']);
            } else {
                $style->getFill()->getStartColor()->setRGB(Color::map($color1, $xls->palette, $xls->version)['rgb']);
                $style->getFill()->getEndColor()->setRGB(Color::map($color2, $xls->palette, $xls->version)['rgb']);
            }
        }
    }

    /*private function getCFProtectionStyle(string $options, Style $style, Xls $xls): void
    {
    }*/

    private function readCFFormula(string $recordData, int $offset, int $size, Xls $xls): float|int|string|null
    {
        try {
            $formula = substr($recordData, $offset, $size);
            $formula = pack('v', $size) . $formula; // prepend the length

            $formula = $xls->getFormulaFromStructure($formula);
            if (is_numeric($formula)) {
                return (str_contains($formula, '.')) ? (float) $formula : (int) $formula;
            }

            return $formula;
        } catch (PhpSpreadsheetException) {
            return null;
        }
    }

    private function setCFRules(array $cellRanges, string $type, string $operator, null|float|int|string $formula1, null|float|int|string $formula2, Style $style, bool $noFormatSet, Xls $xls): void
    {
        foreach ($cellRanges as $cellRange) {
            $conditional = new Conditional();
            $conditional->setNoFormatSet($noFormatSet);
            $conditional->setConditionType($type);
            $conditional->setOperatorType($operator);
            $conditional->setStopIfTrue(true);
            if ($formula1 !== null) {
                $conditional->addCondition($formula1);
            }
            if ($formula2 !== null) {
                $conditional->addCondition($formula2);
            }
            $conditional->setStyle($style);

            $conditionalStyles = $xls->phpSheet->getStyle($cellRange)->getConditionalStyles();
            $conditionalStyles[] = $conditional;

            $xls->phpSheet->getStyle($cellRange)->setConditionalStyles($conditionalStyles);
        }
    }
}
