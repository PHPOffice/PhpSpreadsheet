<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations;

/**
 * @inheritDoc
 */
class XlStdeva extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'STDEVA';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [StandardDeviations::class, 'STDEVA'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
