<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselJ;

/**
 * @inheritDoc
 */
class XlBesselj extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'BESSELJ';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [BesselJ::class, 'BESSELJ'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
