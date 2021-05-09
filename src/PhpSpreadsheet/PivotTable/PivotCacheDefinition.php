<?php

namespace PhpOffice\PhpSpreadsheet\PivotTable;

use PhpOffice\PhpSpreadsheet\PivotTable;
use PhpOffice\PhpSpreadsheet\PivotTable\PivotCacheDefinition\PivotCacheRecords;

class PivotCacheDefinition
{
    /**
     * PivotCacheDefinition target.
     *
     * @var string
     */
    private $target = '';

    /**
     * PivotCacheDefinition name.
     *
     * @var string
     */
    private $name = '';

    /**
     * PivotCacheDefinition id.
     *
     * @var string
     */
    private $id = '';

    /**
     * PivotCacheDefinition data.
     *
     * @var string
     */
    private $data = '';

    /**
     * Collection of PivotCacheRecords.
     *
     * @var PivotCacheRecords[]
     */
    private $pivotCacheRecordsCollection = [];

    /**
     * PivotTable.
     *
     * @var PivotTable
     */
    private $pivotTable;

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

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = preg_replace('/^rId/i', '', $id);
    }

    public function setTarget($target): void
    {
        $this->target = $target;
        $this->name = basename($target);
    }

    public function getXmlData()
    {
        return $this->data;
    }

    public function addPivotCacheRecords(PivotCacheRecords $PivotCacheRecords): void
    {
        $this->pivotCacheRecordsCollection[] = $PivotCacheRecords;
    }

    /**
     * @return PivotCacheRecords[]
     */
    public function getPivotCacheRecordsCollection(): array
    {
        return $this->pivotCacheRecordsCollection;
    }
}
