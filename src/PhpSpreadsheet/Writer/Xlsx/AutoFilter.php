<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet as ActualWorksheet;

class AutoFilter extends WriterPart
{
    /**
     * Write AutoFilter.
     */
    public static function writeAutoFilter(XMLWriter $objWriter, ActualWorksheet $worksheet): void
    {
        $autoFilterRange = $worksheet->getAutoFilter()->getRange();
        if (!empty($autoFilterRange)) {
            // autoFilter
            $objWriter->startElement('autoFilter');

            // Strip any worksheet reference from the filter coordinates
            $range = Coordinate::splitRange($autoFilterRange);
            $range = $range[0];
            //    Strip any worksheet ref
            [$ws, $range[0]] = ActualWorksheet::extractSheetTitle($range[0], true);
            $range = implode(':', $range);

            $objWriter->writeAttribute('ref', str_replace('$', '', $range));

            $columns = $worksheet->getAutoFilter()->getColumns();
            if (count($columns) > 0) {
                foreach ($columns as $columnID => $column) {
                    $colId = $worksheet->getAutoFilter()->getColumnOffset($columnID);
                    self::writeAutoFilterColumn($objWriter, $column, $colId);
                }
            }
            $objWriter->endElement();
        }
    }

    /**
     * Write AutoFilter's filterColumn.
     */
    public static function writeAutoFilterColumn(XMLWriter $objWriter, Column $column, int $colId): void
    {
        $rules = $column->getRules();
        if (count($rules) > 0) {
            $objWriter->startElement('filterColumn');
            $objWriter->writeAttribute('colId', "$colId");

            $objWriter->startElement($column->getFilterType());
            if ($column->getJoin() == Column::AUTOFILTER_COLUMN_JOIN_AND) {
                $objWriter->writeAttribute('and', '1');
            }

            foreach ($rules as $rule) {
                self::writeAutoFilterColumnRule($column, $rule, $objWriter);
            }

            $objWriter->endElement();

            $objWriter->endElement();
        }
    }

    /**
     * Write AutoFilter's filterColumn Rule.
     */
    private static function writeAutoFilterColumnRule(Column $column, Rule $rule, XMLWriter $objWriter): void
    {
        if (
            ($column->getFilterType() === Column::AUTOFILTER_FILTERTYPE_FILTER)
            && ($rule->getOperator() === Rule::AUTOFILTER_COLUMN_RULE_EQUAL)
            && ($rule->getValue() === '')
        ) {
            //    Filter rule for Blanks
            $objWriter->writeAttribute('blank', '1');
        } elseif ($rule->getRuleType() === Rule::AUTOFILTER_RULETYPE_DYNAMICFILTER) {
            //    Dynamic Filter Rule
            $objWriter->writeAttribute('type', $rule->getGrouping());
            $val = $column->getAttribute('val');
            if ($val !== null) {
                $objWriter->writeAttribute('val', "$val");
            }
            $maxVal = $column->getAttribute('maxVal');
            if ($maxVal !== null) {
                $objWriter->writeAttribute('maxVal', "$maxVal");
            }
        } elseif ($rule->getRuleType() === Rule::AUTOFILTER_RULETYPE_TOPTENFILTER) {
            //    Top 10 Filter Rule
            $ruleValue = $rule->getValue();
            if (!is_array($ruleValue)) {
                $objWriter->writeAttribute('val', "$ruleValue");
            }
            $objWriter->writeAttribute('percent', (($rule->getOperator() === Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT) ? '1' : '0'));
            $objWriter->writeAttribute('top', (($rule->getGrouping() === Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP) ? '1' : '0'));
        } else {
            //    Filter, DateGroupItem or CustomFilter
            $objWriter->startElement($rule->getRuleType());

            if ($rule->getOperator() !== Rule::AUTOFILTER_COLUMN_RULE_EQUAL) {
                $objWriter->writeAttribute('operator', $rule->getOperator());
            }
            if ($rule->getRuleType() === Rule::AUTOFILTER_RULETYPE_DATEGROUP) {
                // Date Group filters
                $ruleValue = $rule->getValue();
                if (is_array($ruleValue)) {
                    foreach ($ruleValue as $key => $value) {
                        $objWriter->writeAttribute($key, "$value");
                    }
                }
                $objWriter->writeAttribute('dateTimeGrouping', $rule->getGrouping());
            } else {
                $ruleValue = $rule->getValue();
                if (!is_array($ruleValue)) {
                    $objWriter->writeAttribute('val', "$ruleValue");
                }
            }

            $objWriter->endElement();
        }
    }
}
