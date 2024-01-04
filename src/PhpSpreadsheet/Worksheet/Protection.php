<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Shared\PasswordHasher;

class Protection
{
    const ALGORITHM_MD2 = 'MD2';
    const ALGORITHM_MD4 = 'MD4';
    const ALGORITHM_MD5 = 'MD5';
    const ALGORITHM_SHA_1 = 'SHA-1';
    const ALGORITHM_SHA_256 = 'SHA-256';
    const ALGORITHM_SHA_384 = 'SHA-384';
    const ALGORITHM_SHA_512 = 'SHA-512';
    const ALGORITHM_RIPEMD_128 = 'RIPEMD-128';
    const ALGORITHM_RIPEMD_160 = 'RIPEMD-160';
    const ALGORITHM_WHIRLPOOL = 'WHIRLPOOL';

    /**
     * Autofilters are locked when sheet is protected, default true.
     */
    private ?bool $autoFilter = null;

    /**
     * Deleting columns is locked when sheet is protected, default true.
     */
    private ?bool $deleteColumns = null;

    /**
     * Deleting rows is locked when sheet is protected, default true.
     */
    private ?bool $deleteRows = null;

    /**
     * Formatting cells is locked when sheet is protected, default true.
     */
    private ?bool $formatCells = null;

    /**
     * Formatting columns is locked when sheet is protected, default true.
     */
    private ?bool $formatColumns = null;

    /**
     * Formatting rows is locked when sheet is protected, default true.
     */
    private ?bool $formatRows = null;

    /**
     * Inserting columns is locked when sheet is protected, default true.
     */
    private ?bool $insertColumns = null;

    /**
     * Inserting hyperlinks is locked when sheet is protected, default true.
     */
    private ?bool $insertHyperlinks = null;

    /**
     * Inserting rows is locked when sheet is protected, default true.
     */
    private ?bool $insertRows = null;

    /**
     * Objects are locked when sheet is protected, default false.
     */
    private ?bool $objects = null;

    /**
     * Pivot tables are locked when the sheet is protected, default true.
     */
    private ?bool $pivotTables = null;

    /**
     * Scenarios are locked when sheet is protected, default false.
     */
    private ?bool $scenarios = null;

    /**
     * Selection of locked cells is locked when sheet is protected, default false.
     */
    private ?bool $selectLockedCells = null;

    /**
     * Selection of unlocked cells is locked when sheet is protected, default false.
     */
    private ?bool $selectUnlockedCells = null;

    /**
     * Sheet is locked when sheet is protected, default false.
     */
    private ?bool $sheet = null;

    /**
     * Sorting is locked when sheet is protected, default true.
     */
    private ?bool $sort = null;

    /**
     * Hashed password.
     */
    private string $password = '';

    /**
     * Algorithm name.
     */
    private string $algorithm = '';

    /**
     * Salt value.
     */
    private string $salt = '';

    /**
     * Spin count.
     */
    private int $spinCount = 10000;

    /**
     * Create a new Protection.
     */
    public function __construct()
    {
    }

    /**
     * Is some sort of protection enabled?
     */
    public function isProtectionEnabled(): bool
    {
        return
            $this->password !== ''
            || isset($this->sheet)
            || isset($this->objects)
            || isset($this->scenarios)
            || isset($this->formatCells)
            || isset($this->formatColumns)
            || isset($this->formatRows)
            || isset($this->insertColumns)
            || isset($this->insertRows)
            || isset($this->insertHyperlinks)
            || isset($this->deleteColumns)
            || isset($this->deleteRows)
            || isset($this->selectLockedCells)
            || isset($this->sort)
            || isset($this->autoFilter)
            || isset($this->pivotTables)
            || isset($this->selectUnlockedCells);
    }

    public function getSheet(): ?bool
    {
        return $this->sheet;
    }

    public function setSheet(?bool $sheet): self
    {
        $this->sheet = $sheet;

        return $this;
    }

    public function getObjects(): ?bool
    {
        return $this->objects;
    }

    public function setObjects(?bool $objects): self
    {
        $this->objects = $objects;

        return $this;
    }

    public function getScenarios(): ?bool
    {
        return $this->scenarios;
    }

    public function setScenarios(?bool $scenarios): self
    {
        $this->scenarios = $scenarios;

        return $this;
    }

    public function getFormatCells(): ?bool
    {
        return $this->formatCells;
    }

    public function setFormatCells(?bool $formatCells): self
    {
        $this->formatCells = $formatCells;

        return $this;
    }

    public function getFormatColumns(): ?bool
    {
        return $this->formatColumns;
    }

    public function setFormatColumns(?bool $formatColumns): self
    {
        $this->formatColumns = $formatColumns;

        return $this;
    }

    public function getFormatRows(): ?bool
    {
        return $this->formatRows;
    }

    public function setFormatRows(?bool $formatRows): self
    {
        $this->formatRows = $formatRows;

        return $this;
    }

    public function getInsertColumns(): ?bool
    {
        return $this->insertColumns;
    }

    public function setInsertColumns(?bool $insertColumns): self
    {
        $this->insertColumns = $insertColumns;

        return $this;
    }

    public function getInsertRows(): ?bool
    {
        return $this->insertRows;
    }

    public function setInsertRows(?bool $insertRows): self
    {
        $this->insertRows = $insertRows;

        return $this;
    }

    public function getInsertHyperlinks(): ?bool
    {
        return $this->insertHyperlinks;
    }

    public function setInsertHyperlinks(?bool $insertHyperLinks): self
    {
        $this->insertHyperlinks = $insertHyperLinks;

        return $this;
    }

    public function getDeleteColumns(): ?bool
    {
        return $this->deleteColumns;
    }

    public function setDeleteColumns(?bool $deleteColumns): self
    {
        $this->deleteColumns = $deleteColumns;

        return $this;
    }

    public function getDeleteRows(): ?bool
    {
        return $this->deleteRows;
    }

    public function setDeleteRows(?bool $deleteRows): self
    {
        $this->deleteRows = $deleteRows;

        return $this;
    }

    public function getSelectLockedCells(): ?bool
    {
        return $this->selectLockedCells;
    }

    public function setSelectLockedCells(?bool $selectLockedCells): self
    {
        $this->selectLockedCells = $selectLockedCells;

        return $this;
    }

    public function getSort(): ?bool
    {
        return $this->sort;
    }

    public function setSort(?bool $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getAutoFilter(): ?bool
    {
        return $this->autoFilter;
    }

    public function setAutoFilter(?bool $autoFilter): self
    {
        $this->autoFilter = $autoFilter;

        return $this;
    }

    public function getPivotTables(): ?bool
    {
        return $this->pivotTables;
    }

    public function setPivotTables(?bool $pivotTables): self
    {
        $this->pivotTables = $pivotTables;

        return $this;
    }

    public function getSelectUnlockedCells(): ?bool
    {
        return $this->selectUnlockedCells;
    }

    public function setSelectUnlockedCells(?bool $selectUnlockedCells): self
    {
        $this->selectUnlockedCells = $selectUnlockedCells;

        return $this;
    }

    /**
     * Get hashed password.
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set Password.
     *
     * @param bool $alreadyHashed If the password has already been hashed, set this to true
     *
     * @return $this
     */
    public function setPassword(string $password, bool $alreadyHashed = false): static
    {
        if (!$alreadyHashed) {
            $salt = $this->generateSalt();
            $this->setSalt($salt);
            $password = PasswordHasher::hashPassword($password, $this->getAlgorithm(), $this->getSalt(), $this->getSpinCount());
        }

        $this->password = $password;

        return $this;
    }

    public function setHashValue(string $password): self
    {
        return $this->setPassword($password, true);
    }

    /**
     * Create a pseudorandom string.
     */
    private function generateSalt(): string
    {
        return base64_encode(random_bytes(16));
    }

    /**
     * Get algorithm name.
     */
    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    /**
     * Set algorithm name.
     */
    public function setAlgorithm(string $algorithm): self
    {
        return $this->setAlgorithmName($algorithm);
    }

    /**
     * Set algorithm name.
     */
    public function setAlgorithmName(string $algorithm): self
    {
        $this->algorithm = $algorithm;

        return $this;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        return $this->setSaltValue($salt);
    }

    public function setSaltValue(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get spin count.
     */
    public function getSpinCount(): int
    {
        return $this->spinCount;
    }

    /**
     * Set spin count.
     */
    public function setSpinCount(int $spinCount): self
    {
        $this->spinCount = $spinCount;

        return $this;
    }

    /**
     * Verify that the given non-hashed password can "unlock" the protection.
     */
    public function verify(string $password): bool
    {
        if ($this->password === '') {
            return true;
        }

        $hash = PasswordHasher::hashPassword($password, $this->getAlgorithm(), $this->getSalt(), $this->getSpinCount());

        return $this->getPassword() === $hash;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
