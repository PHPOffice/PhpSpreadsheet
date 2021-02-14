<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Logical;

class Boolean
{
    /**
     * TRUE.
     *
     * Returns the boolean TRUE.
     *
     * Excel Function:
     *        =TRUE()
     *
     * @return bool True
     */
    public static function TRUE(): bool
    {
        return true;
    }

    /**
     * FALSE.
     *
     * Returns the boolean FALSE.
     *
     * Excel Function:
     *        =FALSE()
     *
     * @return bool False
     */
    public static function FALSE(): bool
    {
        return false;
    }
}
