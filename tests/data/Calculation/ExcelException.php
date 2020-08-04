<?php

use PhpOffice\PhpSpreadsheet\Calculation\ExcelException;

return [
    ['#NULL!', ExcelException::NULL()],
    ['#DIV/0!', ExcelException::DIV0()],
    ['#VALUE!', ExcelException::VALUE()],
    ['#NUM!', ExcelException::NUM()],
];
