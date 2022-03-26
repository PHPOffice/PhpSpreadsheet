<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

/**
 * @inheritDoc
 */
class XlNa extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'NA';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_INFORMATION;

    /**
     * @var callable
     */
    protected $functionCall = [ExcelError::class, 'NA'];

    /**
     * @var string
     */
    protected $argumentCount = '0';
}
