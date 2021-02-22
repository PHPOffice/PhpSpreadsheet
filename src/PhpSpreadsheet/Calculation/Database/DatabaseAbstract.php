<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

abstract class DatabaseAbstract
{
    abstract public static function evaluate($database, $field, $criteria);

    /**
     * fieldExtract.
     *
     * Extracts the column ID to use for the data field.
     *
     * @param mixed[] $database The range of cells that makes up the list or database.
     *                                        A database is a list of related data in which rows of related
     *                                        information are records, and columns of data are fields. The
     *                                        first row of the list contains labels for each column.
     * @param mixed $field Indicates which column is used in the function. Enter the
     *                                        column label enclosed between double quotation marks, such as
     *                                        "Age" or "Yield," or a number (without quotation marks) that
     *                                        represents the position of the column within the list: 1 for
     *                                        the first column, 2 for the second column, and so on.
     */
    protected static function fieldExtract(array $database, $field): ?string
    {
        $field = strtoupper(Functions::flattenSingleValue($field));
        $fieldNames = array_map('strtoupper', array_shift($database));

        if (is_numeric($field)) {
            $keys = array_keys($fieldNames);

            return $keys[$field - 1];
        }
        $key = array_search($field, $fieldNames);

        return $key ?: null;
    }

    /**
     * filter.
     *
     * Parses the selection criteria, extracts the database rows that match those criteria, and
     * returns that subset of rows.
     *
     * @param mixed[] $database The range of cells that makes up the list or database.
     *                                        A database is a list of related data in which rows of related
     *                                        information are records, and columns of data are fields. The
     *                                        first row of the list contains labels for each column.
     * @param mixed[] $criteria The range of cells that contains the conditions you specify.
     *                                        You can use any range for the criteria argument, as long as it
     *                                        includes at least one column label and at least one cell below
     *                                        the column label in which you specify a condition for the
     *                                        column.
     *
     * @return array of mixed
     */
    protected static function filter(array $database, array $criteria): array
    {
        $fieldNames = array_shift($database);
        $criteriaNames = array_shift($criteria);

        //    Convert the criteria into a set of AND/OR conditions with [:placeholders]
        $query = self::buildQuery($criteriaNames, $criteria);

        //    Loop through each row of the database
        return self::executeQuery($database, $query, $criteriaNames, $fieldNames);
    }

    protected static function getFilteredColumn(array $database, $field, array $criteria): array
    {
        //    reduce the database to a set of rows that match all the criteria
        $database = self::filter($database, $criteria);
        //    extract an array of values for the requested column
        $columnData = [];
        foreach ($database as $row) {
            $columnData[] = ($field !== null) ? $row[$field] : true;
        }

        return $columnData;
    }

    /**
     * @TODO Support for Dates (including handling for >, <=, etc)
     * @TODO Suport for formatted numerics (e.g. '>12.5%' => '>0.125')
     * @TODO Suport for wildcard ? and * in strings (includng escaping)
     */
    private static function buildQuery(array $criteriaNames, array $criteria): string
    {
        $baseQuery = [];
        foreach ($criteria as $key => $criterion) {
            foreach ($criterion as $field => $value) {
                $criterionName = $criteriaNames[$field];
                if ($value !== null && $value !== '') {
                    $condition = '[:' . $criterionName . ']' . Functions::ifCondition($value);
                    $baseQuery[$key][] = $condition;
                }
            }
        }

        $rowQuery = array_map(
            function ($rowValue) {
                return (count($rowValue) > 1) ? 'AND(' . implode(',', $rowValue) . ')' : $rowValue[0];
            },
            $baseQuery
        );

        return (count($rowQuery) > 1) ? 'OR(' . implode(',', $rowQuery) . ')' : $rowQuery[0];
    }

    /**
     * @param $criteriaNames
     * @param $fieldNames
     */
    private static function executeQuery(array $database, string $query, $criteriaNames, $fieldNames): array
    {
        foreach ($database as $dataRow => $dataValues) {
            //    Substitute actual values from the database row for our [:placeholders]
            $testConditionList = $query;
            foreach ($criteriaNames as $key => $criteriaName) {
                $key = array_search($criteriaName, $fieldNames, true);
                if (isset($dataValues[$key])) {
                    $dataValue = $dataValues[$key];
                    $dataValue = (is_string($dataValue)) ? Calculation::wrapResult(strtoupper($dataValue)) : $dataValue;
                } else {
                    $dataValue = 'NULL';
                }
                $testConditionList = str_replace('[:' . $criteriaName . ']', $dataValue, $testConditionList);
            }

            //    evaluate the criteria against the row data
            $result = Calculation::getInstance()->_calculateFormulaValue('=' . $testConditionList);
            //    If the row failed to meet the criteria, remove it from the database

            if ($result !== true) {
                unset($database[$dataRow]);
            }
        }

        return $database;
    }
}
