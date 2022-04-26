<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ErfC;

/**
 * @inheritDoc
 */
class XlErfc extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ERFC';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [ErfC::class, 'ERFC'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
