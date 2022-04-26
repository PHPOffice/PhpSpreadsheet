<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Counts;

/**
 * @inheritDoc
 */
class XlCounta extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'COUNTA';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Counts::class, 'COUNTA'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
