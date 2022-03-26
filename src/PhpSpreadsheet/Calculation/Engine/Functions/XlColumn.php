<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlPassByReference;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlPassCellReference;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\RowColumnInformation;

/**
 * @inheritDoc
 */
class XlColumn extends XlFunctionAbstract
{
    use XlPassByReference;
    use XlPassCellReference;

    /**
     * @var string
     */
    protected $name = 'COLUMN';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOOKUP_AND_REFERENCE;

    /**
     * @var callable
     */
    protected $functionCall = [RowColumnInformation::class, 'COLUMN'];

    /**
     * @var string
     */
    protected $argumentCount = '-1';
}
