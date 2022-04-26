<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Fisher;

/**
 * @inheritDoc
 */
class XlFisherinv extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'FISHERINV';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Fisher::class, 'inverse'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
