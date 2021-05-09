<?php

namespace PhpOffice\PhpSpreadsheet\PivotTable\PivotCacheDefinition;

use PhpOffice\PhpSpreadsheet\PivotTable\PivotCacheDefinition;

class PivotCacheRecords
{
    /**
     * PivotCacheRecords target.
     *
     * @var string
     */
    private $target = '';

    /**
     * PivotCacheRecords name.
     *
     * @var string
     */
    private $name = '';

    /**
     * PivotCacheRecords id.
     *
     * @var string
     */
    private $id = '';

    /**
     * PivotCacheRecords data.
     *
     * @var string
     */
    private $data = '';

    /**
     * PivotCacheDefinition.
     *
     * @var null|PivotCacheDefinition
     */
    private $pivotCacheDefinition;

    public function __construct($id, $target, $xmlData)
    {
        $this->setId($id);
        $this->setTarget($target);
        $this->data = $xmlData;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setTarget($target): void
    {
        $this->target = $target;
        $this->name = basename($target);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = preg_replace('/^rId/i', '', $id);
    }

    public function getXmlData()
    {
        return $this->data;
    }
}
