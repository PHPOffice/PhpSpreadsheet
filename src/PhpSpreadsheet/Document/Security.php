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
     */
    public function isSecurityEnabled(): bool
    {
        return  $this->lockRevision ||
                $this->lockStructure ||
                $this->lockWindows;
    }

    public function getLockRevision(): bool
    {
        return $this->lockRevision;
    }

    public function setLockRevision(?bool $pValue): self
    {
        if ($pValue !== null) {
            $this->lockRevision = $pValue;
        }

        return $this;
    }

    public function getLockStructure(): bool
    {
        return $this->lockStructure;
    }

    public function setLockStructure(?bool $pValue): self
    {
        if ($pValue !== null) {
            $this->lockStructure = $pValue;
        }

        return $this;
    }

    public function getLockWindows(): bool
    {
        return $this->lockWindows;
    }

    public function setLockWindows(?bool $pValue): self
    {
        if ($pValue !== null) {
            $this->lockWindows = $pValue;
        }

        return $this;
    }

    public function getRevisionsPassword(): string
    {
        return $this->revisionsPassword;
    }

    public function setRevisionsPassword(?string $pValue, bool $pAlreadyHashed = false): self
    {
        if ($pValue !== null) {
            if (!$pAlreadyHashed) {
                $pValue = PasswordHasher::hashPassword($pValue);
            }
            $this->revisionsPassword = $pValue;
        }

        return $this;
    }

    public function getWorkbookPassword(): string
    {
        return $this->workbookPassword;
    }

    public function setWorkbookPassword(?string $pValue, bool $pAlreadyHashed = false): self
    {
        if ($pValue !== null) {
            if (!$pAlreadyHashed) {
                $pValue = PasswordHasher::hashPassword($pValue);
            }
            $this->workbookPassword = $pValue;
        }

        return $this;
    }
}
