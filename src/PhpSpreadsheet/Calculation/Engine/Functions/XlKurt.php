<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Deviations;

/**
 * @inheritDoc
 */
class XlKurt extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'KURT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Deviations::class, 'kurtosis'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
