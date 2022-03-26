<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise;

/**
 * @inheritDoc
 */
class XlBitrshift extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'BITRSHIFT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [BitWise::class, 'BITRSHIFT'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
