<?php

class Complex {

	private $realPart = 0;
	private $imaginaryPart = 0;
	private $suffix = 'i';

	public static function _parseComplex($complexNumber) {
		$complexNumber = (string) $complexNumber;

		$validComplex = preg_match('/^([-+]?(\d+\.?\d*|\d*\.?\d+)([Ee][-+]?[0-2]?\d{1,2})?)([-+]?(\d+\.?\d*|\d*\.?\d+)([Ee][-+]?[0-2]?\d{1,2})?)?(([-+]?)([ij]?))$/ui',$complexNumber,$complexParts);

		if (!$validComplex) {
			return array( $complexNumber, 0, '' );
		}

		if (($complexParts[4] === '') && ($complexParts[9] !== '')) {
			$complexParts[4] = $complexParts[8] . 1;
		}

		return array( (float) $complexParts[1],
					  (float) $complexParts[4],
					  $complexParts[9]
					);
	}	//	function _parseComplex()


	public function __construct($realPart, $imaginaryPart = null, $suffix = null)
	{
		if ($imaginaryPart === null) {
			if (is_array($realPart)) {
				list ($realPart, $imaginaryPart, $suffix) = $realPart;
			} elseif((is_string($realPart)) || (is_numeric($realPart))) {
				list ($realPart, $imaginaryPart, $suffix) = self::_parseComplex($realPart);
			}
		}

		$this->realPart = $realPart;
		$this->imaginaryPart = $imaginaryPart;
		$this->suffix = strtolower($suffix);
	}

	public function getReal()
	{
		return $this->realPart;
	}

	public function getImaginary()
	{
		return $this->imaginaryPart;
	}

	public function getSuffix()
	{
		return $this->suffix;
	}

	public function __toString() {
		$str = "";
		if ($this->imaginaryPart != 0.0) $str .= $this->imaginaryPart . $this->suffix;
		if ($this->realPart != 0.0) {
			if ($str) $str = "+" . $str;
			$str = $this->realPart . $str;
		}
		if (!$str) $str = "0";
		return $str;
	}

}
