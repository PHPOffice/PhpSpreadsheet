<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselI;

/**
 * @inheritDoc
 */
class XlBesseli extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'BESSELI';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [BesselI::class, 'BESSELI'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
