<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations;

/**
 * @inheritDoc
 */
class XlStdevpa extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'STDEVPA';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [StandardDeviations::class, 'STDEVPA'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
