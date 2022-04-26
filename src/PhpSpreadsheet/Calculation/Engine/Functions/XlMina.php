<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Minimum;

/**
 * @inheritDoc
 */
class XlMina extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'MINA';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Minimum::class, 'minA'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
