<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract;

/**
 * @inheritDoc
 */
class XlRightb extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'RIGHTB';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [Extract::class, 'right'];

    /**
     * @var string
     */
    protected $argumentCount = '1,2';
}
