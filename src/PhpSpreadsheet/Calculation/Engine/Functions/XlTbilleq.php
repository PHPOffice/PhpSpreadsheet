<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\TreasuryBill;

/**
 * @inheritDoc
 */
class XlTbilleq extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'TBILLEQ';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [TreasuryBill::class, 'bondEquivalentYield'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
