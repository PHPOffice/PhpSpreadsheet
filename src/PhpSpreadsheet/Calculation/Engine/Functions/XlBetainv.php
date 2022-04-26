<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Beta;

/**
 * @inheritDoc
 */
class XlBetainv extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'BETAINV';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Beta::class, 'inverse'];

    /**
     * @var string
     */
    protected $argumentCount = '3-5';
}
