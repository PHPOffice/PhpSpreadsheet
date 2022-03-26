<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Permutations;

/**
 * @inheritDoc
 */
class XlPermutationa extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'PERMUTATIONA';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Permutations::class, 'PERMUTATIONA'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
