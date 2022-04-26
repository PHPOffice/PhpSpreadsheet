<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Fisher;

/**
 * @inheritDoc
 */
class XlFisher extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'FISHER';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Fisher::class, 'distribution'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
