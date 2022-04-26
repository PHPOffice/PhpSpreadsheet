<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertUOM;

/**
 * @inheritDoc
 */
class XlConvert extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'CONVERT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [ConvertUOM::class, 'CONVERT'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
