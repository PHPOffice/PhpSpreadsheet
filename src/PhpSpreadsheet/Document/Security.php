<?php

namespace PhpOffice\PhpSpreadsheet\Document;

use PhpOffice\PhpSpreadsheet\Shared\PasswordHasher;

class Security
{
    /**
     * LockRevision.
     *
     * @var bool
     */
    private $lockRevision = false;

    /**
     * LockStructure.
     *
     * @var bool
     */
    private $lockStructure = false;

    /**
     * LockWindows.
     *
     * @var bool
     */
    private $lockWindows = false;

    /**
     * RevisionsPassword.
     *
     * @var string
     */
    private $revisionsPassword = '';

    /**
     * WorkbookPassword.
     *
     * @var string
     */
    private $workbookPassword = '';

    /**
     * Create a new Document Security instance.
     */
    public function __construct()
    {
    }

    /**
     * Is some sort of document security enabled?
     *
     * @return bool
     */
    public function isSecurityEnabled()
    {
        return  $this->lockRevision ||
                $this->lockStructure ||
                $this->lockWindows;
    }

    /**
     * Get LockRevision.
     *
     * @return bool
     */
    public function getLockRevision()
    {
        return $this->lockRevision;
    }

    /**
     * Set LockRevision.
     *
     * @param bool $pValue
     *
     * @return $this
     */
    public function setLockRevision($pValue)
    {
        $this->lockRevision = $pValue;

        return $this;
    }

    /**
     * Get LockStructure.
     *
     * @return bool
     */
    public function getLockStructure()
    {
        return $this->lockStructure;
    }

    /**
     * Set LockStructure.
     *
     * @param bool $pValue
     *
     * @return $this
     */
    public function setLockStructure($pValue)
    {
        $this->lockStructure = $pValue;

        return $this;
    }

    /**
     * Get LockWindows.
     *
     * @return bool
     */
    public function getLockWindows()
    {
        return $this->lockWindows;
    }

    /**
     * Set LockWindows.
     *
     * @param bool $pValue
     *
     * @return $this
     */
    public function setLockWindows($pValue)
    {
        $this->lockWindows = $pValue;

        return $this;
    }

    /**
     * Get RevisionsPassword (hashed).
     *
     * @return string
     */
    public function getRevisionsPassword()
    {
        return $this->revisionsPassword;
    }

    /**
     * Set RevisionsPassword.
     *
     * @param string $pValue
     * @param bool $pAlreadyHashed If the password has already been hashed, set this to true
     *
     * @return $this
     */
    public function setRevisionsPassword($pValue, $pAlreadyHashed = false)
    {
        if (!$pAlreadyHashed) {
            $pValue = PasswordHasher::hashPassword($pValue);
        }
        $this->revisionsPassword = $pValue;

        return $this;
    }

    /**
     * Get WorkbookPassword (hashed).
     *
     * @return string
     */
    public function getWorkbookPassword()
    {
        return $this->workbookPassword;
    }

    /**
     * Set WorkbookPassword.
     *
     * @param string $pValue
     * @param bool $pAlreadyHashed If the password has already been hashed, set this to true
     *
     * @return $this
     */
    public function setWorkbookPassword($pValue, $pAlreadyHashed = false)
    {
        if (!$pAlreadyHashed) {
            $pValue = PasswordHasher::hashPassword($pValue);
        }
        $this->workbookPassword = $pValue;

        return $this;
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
