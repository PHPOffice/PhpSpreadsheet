<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations;

class DStDev extends DatabaseAbstract
{
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
     * @param null|array|int|string $field Indicates which column is used in the function. Enter the
     *                                        column label enclosed between double quotation marks, such as
     *                                        "Age" or "Yield," or a number (without quotation marks) that
     *                                        represents the position of the column within the list: 1 for
     *                                        the first column, 2 for the second column, and so on.
     * @param mixed[] $criteria The range of cells that contains the conditions you specify.
     *                                        You can use any range for the criteria argument, as long as it
     *                                        includes at least one column label and at least one cell below
     *                                        the column label in which you specify a condition for the
     *                                        column.
     */
    public static function evaluate(array $database, array|null|int|string $field, array $criteria): float|string
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return ExcelError::VALUE();
        }

        return StandardDeviations::STDEV(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }
}
