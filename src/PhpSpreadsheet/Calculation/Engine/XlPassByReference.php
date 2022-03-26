<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine;

/**
 * @property bool[] $passByReference
 */
trait XlPassByReference
{
    /**
     * @var bool[]
     */
    protected $passByReference = [true];
}
