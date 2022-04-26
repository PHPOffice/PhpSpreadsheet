<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselY;

/**
 * @inheritDoc
 */
class XlBessely extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'BESSELY';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [BesselY::class, 'BESSELY'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
