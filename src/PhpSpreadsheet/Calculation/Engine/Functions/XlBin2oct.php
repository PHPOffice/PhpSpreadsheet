<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertBinary;

/**
 * @inheritDoc
 */
class XlBin2oct extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'BIN2OCT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [ConvertBinary::class, 'toOctal'];

    /**
     * @var string
     */
    protected $argumentCount = '1,2';
}
