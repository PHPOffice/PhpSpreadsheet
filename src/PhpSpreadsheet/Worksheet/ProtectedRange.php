<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

class ProtectedRange
{
    private string $name = '';

    private string $password = '';

    private string $sqref;

    private string $securityDescriptor = '';

    /**
     * No setters aside from constructor.
     */
    public function __construct(string $sqref, string $password = '', string $name = '', string $securityDescriptor = '')
    {
        $this->sqref = $sqref;
        $this->name = $name;
        $this->password = $password;
        $this->securityDescriptor = $securityDescriptor;
    }

    public function getSqref(): string
    {
        return $this->sqref;
    }

    public function getName(): string
    {
        return $this->name ?: ('p' . md5($this->sqref));
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSecurityDescriptor(): string
    {
        return $this->securityDescriptor;
    }
}
