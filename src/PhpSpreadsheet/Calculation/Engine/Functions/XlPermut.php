<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Permutations;

/**
 * @inheritDoc
 */
class XlPermut extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'PERMUT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Permutations::class, 'PERMUT'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
