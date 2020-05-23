<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

class Database
{
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
     *
     * @return null|string
     */
    private static function fieldExtract($database, $field)
    {
        $field = strtoupper(Functions::flattenSingleValue($field));
        $fieldNames = array_map('strtoupper', array_shift($database));

        if (is_numeric($field)) {
            $keys = array_keys($fieldNames);

            return $keys[$field - 1];
        }
        $key = array_search($field, $fieldNames);

        return ($key) ? $key : null;
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
    private static function filter($database, $criteria)
    {
        $fieldNames = array_shift($database);
        $criteriaNames = array_shift($criteria);

        //    Convert the criteria into a set of AND/OR conditions with [:placeholders]
        $testConditions = $testValues = [];
        $testConditionsCount = 0;
        foreach ($criteriaNames as $key => $criteriaName) {
            $testCondition = [];
            $testConditionCount = 0;
            foreach ($criteria as $row => $criterion) {
                if ($criterion[$key] > '') {
                    $testCondition[] = '[:' . $criteriaName . ']' . Functions::ifCondition($criterion[$key]);
                    ++$testConditionCount;
                }
            }
            if ($testConditionCount > 1) {
                $testConditions[] = 'OR(' . implode(',', $testCondition) . ')';
                ++$testConditionsCount;
            } elseif ($testConditionCount == 1) {
                $testConditions[] = $testCondition[0];
                ++$testConditionsCount;
            }
        }

        if ($testConditionsCount > 1) {
            $testConditionSet = 'AND(' . implode(',', $testConditions) . ')';
        } elseif ($testConditionsCount == 1) {
            $testConditionSet = $testConditions[0];
        }

        //    Loop through each row of the database
        foreach ($database as $dataRow => $dataValues) {
            //    Substitute actual values from the database row for our [:placeholders]
            $testConditionList = $testConditionSet;
            foreach ($criteriaNames as $key => $criteriaName) {
                $k = array_search($criteriaName, $fieldNames);
                if (isset($dataValues[$k])) {
                    $dataValue = $dataValues[$k];
                    $dataValue = (is_string($dataValue)) ? Calculation::wrapResult(strtoupper($dataValue)) : $dataValue;
                    $testConditionList = str_replace('[:' . $criteriaName . ']', $dataValue, $testConditionList);
                }
            }
            //    evaluate the criteria against the row data
            $result = Calculation::getInstance()->_calculateFormulaValue('=' . $testConditionList);
            //    If the row failed to meet the criteria, remove it from the database
            if (!$result) {
                unset($database[$dataRow]);
            }
        }

        return $database;
    }

    private static function getFilteredColumn($database, $field, $criteria)
    {
        //    reduce the database to a set of rows that match all the criteria
        $database = self::filter($database, $criteria);
        //    extract an array of values for the requested column
        $colData = [];
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }

        return $colData;
    }

    /**
     * DAVERAGE.
     *
     * Averages the values in a column of a list or database that match conditions you specify.
     *
     * Excel Function:
     *        DAVERAGE(database,field,criteria)
     *
     * @param mixed[] $database The range of cells that makes up the list or database.
     *                                        A database is a list of related data in which rows of related
     *                                        information are records, and columns of data are fields. The
     *                                        first row of the list contains labels for each column.
     * @param int|string $field Indicates which column is used in the function. Enter the
     *                                        column label enclosed between double quotation marks, such as
     *                                        "Age" or "Yield," or a number (without quotation marks) that
     *                                        represents the position of the column within the list: 1 for
     *                                        the first column, 2 for the second column, and so on.
     * @param mixed[] $criteria The range of cells that contains the conditions you specify.
     *                                        You can use any range for the criteria argument, as long as it
     *                                        includes at least one column label and at least one cell below
     *                                        the column label in which you specify a condition for the
     *                                        column.
     *
     * @return float|string
     */
    public static function DAVERAGE($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        // Return
        return Statistical::AVERAGE(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    /**
     * DCOUNT.
     *
     * Counts the cells that contain numbers in a column of a list or database that match conditions
     * that you specify.
     *
     * Excel Function:
     *        DCOUNT(database,[field],criteria)
     *
     * Excel Function:
     *        DAVERAGE(database,field,criteria)
     *
     * @param mixed[] $database The range of cells that makes up the list or database.
     *                                        A database is a list of related data in which rows of related
     *                                        information are records, and columns of data are fields. The
     *                                        first row of the list contains labels for each column.
     * @param int|string $field Indicates which column is used in the function. Enter the
     *                                        column label enclosed between double quotation marks, such as
     *                                        "Age" or "Yield," or a number (without quotation marks) that
     *                                        represents the position of the column within the list: 1 for
     *                                        the first column, 2 for the second column, and so on.
     * @param mixed[] $criteria The range of cells that contains the conditions you specify.
     *                                        You can use any range for the criteria argument, as long as it
     *                                        includes at least one column label and at least one cell below
     *                                        the column label in which you specify a condition for the
     *                                        column.
     *
     * @return int
     *
     * @TODO    The field argument is optional. If field is omitted, DCOUNT counts all records in the
     *            database that match the criteria.
     */
    public static function DCOUNT($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        // Return
        return Statistical::COUNT(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    /**
     * DCOUNTA.
     *
     * Counts the nonblank cells in a column of a list or database that match conditions that you specify.
     *
     * Excel Function:
     *        DCOUNTA(database,[field],criteria)
     *
     * @param mixed[] $database The range of cells that makes up the list or database.
     *                                        A database is a list of related data in which rows of related
     *                                        information are records, and columns of data are fields. The
     *                                        first row of the list contains labels for each column.
     * @param int|string $field Indicates which column is used in the function. Enter the
     *                                        column label enclosed between double quotation marks, such as
     *                                        "Age" or "Yield," or a number (without quotation marks) that
     *                                        represents the position of the column within the list: 1 for
     *                                        the first column, 2 for the second column, and so on.
     * @param mixed[] $criteria The range of cells that contains the conditions you specify.
     *                                        You can use any range for the criteria argument, as long as it
     *                                        includes at least one column label and at least one cell below
     *                                        the column label in which you specify a condition for the
     *                                        column.
     *
     * @return int
     *
     * @TODO    The field argument is optional. If field is omitted, DCOUNTA counts all records in the
     *            database that match the criteria.
     */
    public static function DCOUNTA($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        //    reduce the database to a set of rows that match all the criteria
        $database = self::filter($database, $criteria);
        //    extract an array of values for the requested column
        $colData = [];
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }

        // Return
        return Statistical::COUNTA(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    /**
     * DGET.
     *
     * Extracts a single value from a column of a list or database that matches conditions that you
     * specify.
     *
     * Excel Function:
     *        DGET(database,field,criteria)
     *
     * @param mixed[] $database The range of cells that makes up the list or database.
     *                                        A database is a list of related data in which rows of related
     *                                        information are records, and columns of data are fields. The
     *                                        first row of the list contains labels for each column.
     * @param int|string $field Indicates which column is used in the function. Enter the
     *                                        column label enclosed between double quotation marks, such as
     *                                        "Age" or "Yield," or a number (without quotation marks) that
     *                                        represents the position of the column within the list: 1 for
     *                                        the first column, 2 for the second column, and so on.
     * @param mixed[] $criteria The range of cells that contains the conditions you specify.
     *                                        You can use any range for the criteria argument, as long as it
     *                                        includes at least one column label and at least one cell below
     *                                        the column label in which you specify a condition for the
     *                                        column.
     *
     * @return mixed
     */
    public static function DGET($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        // Return
        $colData = self::getFilteredColumn($database, $field, $criteria);
        if (count($colData) > 1) {
            return Functions::NAN();
        }

        return $colData[0];
    }

    /**
     * DMAX.
     *
     * Returns the largest number in a column of a list or database that matches conditions you that
     * specify.
     *
     * Excel Function:
     *        DMAX(database,field,criteria)
     *
     * @param mixed[] $database The range of cells that makes up the list or database.
     *                                        A database is a list of related data in which rows of related
     *                                        information are records, and columns of data are fields. The
     *                                        first row of the list contains labels for each column.
     * @param int|string $field Indicates which column is used in the function. Enter the
     *                                        column label enclosed between double quotation marks, such as
     *                                        "Age" or "Yield," or a number (without quotation marks) that
     *                                        represents the position of the column within the list: 1 for
     *                                        the first column, 2 for the second column, and so on.
     * @param mixed[] $criteria The range of cells that contains the conditions you specify.
     *                                        You can use any range for the criteria argument, as long as it
     *                                        includes at least one column label and at least one cell below
     *                                        the column label in which you specify a condition for the
     *                                        column.
     *
     * @return float
     */
    public static function DMAX($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        // Return
        return Statistical::MAX(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    /**
     * DMIN.
     *
     * Returns the smallest number in a column of a list or database that matches conditions you that
     * specify.
     *
     * Excel Function:
     *        DMIN(database,field,criteria)
     *
     * @param mixed[] $database The range of cells that makes up the list or database.
     *                                        A database is a list of related data in which rows of related
     *                                        information are records, and columns of data are fields. The
     *                                        first row of the list contains labels for each column.
     * @param int|string $field Indicates which column is used in the function. Enter the
     *                                        column label enclosed between double quotation marks, such as
     *                                        "Age" or "Yield," or a number (without quotation marks) that
     *                                        represents the position of the column within the list: 1 for
     *                                        the first column, 2 for the second column, and so on.
     * @param mixed[] $criteria The range of cells that contains the conditions you specify.
     *                                        You can use any range for the criteria argument, as long as it
     *                                        includes at least one column label and at least one cell below
     *                                        the column label in which you specify a condition for the
     *                                        column.
     *
     * @return float
     */
    public static function DMIN($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        // Return
        return Statistical::MIN(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    /**
     * DPRODUCT.
     *
     * Multiplies the values in a column of a list or database that match conditions that you specify.
     *
     * Excel Function:
     *        DPRODUCT(database,field,criteria)
     *
     * @param mixed[] $database The range of cells that makes up the list or database.
     *                                        A database is a list of related data in which rows of related
     *                                        information are records, and columns of data are fields. The
     *                                        first row of the list contains labels for each column.
     * @param int|string $field Indicates which column is used in the function. Enter the
     *                                        column label enclosed between double quotation marks, such as
     *                                        "Age" or "Yield," or a number (without quotation marks) that
     *                                        represents the position of the column within the list: 1 for
     *                                        the first column, 2 for the second column, and so on.
     * @param mixed[] $criteria The range of cells that contains the conditions you specify.
     *                                        You can use any range for the criteria argument, as long as it
     *                                        includes at least one column label and at least one cell below
     *                                        the column label in which you specify a condition for the
     *                                        column.
     *
     * @return float
     */
    public static function DPRODUCT($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        // Return
        return MathTrig::PRODUCT(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    /**
     * DSTDEV.
     *
     * Estimates the standard deviation of a population based on a sample by using the numbers in a
     * column of a list or database that match conditions that you specify.
     *
     * Excel Function:
     *        DSTDEV(database,field,criteria)
     *
     * @param mixed[] $database The range of cells that makes up the list or database.
     *                                        A database is a list of related data in which rows of related
     *                                        information are records, and columns of data are fields. The
     *                                        first row of the list contains labels for each column.
     * @param int|string $field Indicates which column is used in the function. Enter the
     *                                        column label enclosed between double quotation marks, such as
     *                                        "Age" or "Yield," or a number (without quotation marks) that
     *                                        represents the position of the column within the list: 1 for
     *                                        the first column, 2 for the second column, and so on.
     * @param mixed[] $criteria The range of cells that contains the conditions you specify.
     *                                        You can use any range for the criteria argument, as long as it
     *                                        includes at least one column label and at least one cell below
     *                                        the column label in which you specify a condition for the
     *                                        column.
     *
     * @return float|string
     */
    public static function DSTDEV($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        // Return
        return Statistical::STDEV(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    /**
     * DSTDEVP.
     *
     * Calculates the standard deviation of a population based on the entire population by using the
     * numbers in a column of a list or database that match conditions that you specify.
     *
     * Excel Function:
     *        DSTDEVP(database,field,criteria)
     *
     * @param mixed[] $database The range of cells that makes up the list or database.
     *                                        A database is a list of related data in which rows of related
     *                                        information are records, and columns of data are fields. The
     *                                        first row of the list contains labels for each column.
     * @param int|string $field Indicates which column is used in the function. Enter the
     *                                        column label enclosed between double quotation marks, such as
     *                                        "Age" or "Yield," or a number (without quotation marks) that
     *                                        represents the position of the column within the list: 1 for
     *                                        the first column, 2 for the second column, and so on.
     * @param mixed[] $criteria The range of cells that contains the conditions you specify.
     *                                        You can use any range for the criteria argument, as long as it
     *                                        includes at least one column label and at least one cell below
     *                                        the column label in which you specify a condition for the
     *                                        column.
     *
     * @return float|string
     */
    public static function DSTDEVP($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        // Return
        return Statistical::STDEVP(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    /**
     * DSUM.
     *
     * Adds the numbers in a column of a list or database that match conditions that you specify.
     *
     * Excel Function:
     *        DSUM(database,field,criteria)
     *
     * @param mixed[] $database The range of cells that makes up the list or database.
     *                                        A database is a list of related data in which rows of related
     *                                        information are records, and columns of data are fields. The
     *                                        first row of the list contains labels for each column.
     * @param int|string $field Indicates which column is used in the function. Enter the
     *                                        column label enclosed between double quotation marks, such as
     *                                        "Age" or "Yield," or a number (without quotation marks) that
     *                                        represents the position of the column within the list: 1 for
     *                                        the first column, 2 for the second column, and so on.
     * @param mixed[] $criteria The range of cells that contains the conditions you specify.
     *                                        You can use any range for the criteria argument, as long as it
     *                                        includes at least one column label and at least one cell below
     *                                        the column label in which you specify a condition for the
     *                                        column.
     *
     * @return float
     */
    public static function DSUM($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        // Return
        return MathTrig::SUM(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    /**
     * DVAR.
     *
     * Estimates the variance of a population based on a sample by using the numbers in a column
     * of a list or database that match conditions that you specify.
     *
     * Excel Function:
     *        DVAR(database,field,criteria)
     *
     * @param mixed[] $database The range of cells that makes up the list or database.
     *                                        A database is a list of related data in which rows of related
     *                                        information are records, and columns of data are fields. The
     *                                        first row of the list contains labels for each column.
     * @param int|string $field Indicates which column is used in the function. Enter the
     *                                        column label enclosed between double quotation marks, such as
     *                                        "Age" or "Yield," or a number (without quotation marks) that
     *                                        represents the position of the column within the list: 1 for
     *                                        the first column, 2 for the second column, and so on.
     * @param mixed[] $criteria The range of cells that contains the conditions you specify.
     *                                        You can use any range for the criteria argument, as long as it
     *                                        includes at least one column label and at least one cell below
     *                                        the column label in which you specify a condition for the
     *                                        column.
     *
     * @return float
     */
    public static function DVAR($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        // Return
        return Statistical::VARFunc(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    /**
     * DVARP.
     *
     * Calculates the variance of a population based on the entire population by using the numbers
     * in a column of a list or database that match conditions that you specify.
     *
     * Excel Function:
     *        DVARP(database,field,criteria)
     *
     * @param mixed[] $database The range of cells that makes up the list or database.
     *                                        A database is a list of related data in which rows of related
     *                                        information are records, and columns of data are fields. The
     *                                        first row of the list contains labels for each column.
     * @param int|string $field Indicates which column is used in the function. Enter the
     *                                        column label enclosed between double quotation marks, such as
     *                                        "Age" or "Yield," or a number (without quotation marks) that
     *                                        represents the position of the column within the list: 1 for
     *                                        the first column, 2 for the second column, and so on.
     * @param mixed[] $criteria The range of cells that contains the conditions you specify.
     *                                        You can use any range for the criteria argument, as long as it
     *                                        includes at least one column label and at least one cell below
     *                                        the column label in which you specify a condition for the
     *                                        column.
     *
     * @return float
     */
    public static function DVARP($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        // Return
        return Statistical::VARP(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }
}
