<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselK;

/**
 * @inheritDoc
 */
class XlBesselk extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'BESSELK';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [BesselK::class, 'BESSELK'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
