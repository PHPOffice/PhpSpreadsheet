<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

/**
 * @inheritDoc
 */
class XlError_type extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ERROR.TYPE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_INFORMATION;

    /**
     * @var callable
     */
    protected $functionCall = [ExcelError::class, 'type'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
