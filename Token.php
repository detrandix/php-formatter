<?php

class Token
{

	protected $value;

	protected $type;

	public function __construct($value, $type = NULL)
	{
		$this->value = $value;
		$this->type = $type;
	}

	public static function createFromZendToken($zendToken)
	{
		if (is_array($zendToken)) {
			$value = $zendToken[1];
			$type = $zendToken[0];
		} else {
			$value = $zendToken;
			$type = NULL;
		}

		return new self($value, $type);
	}

	public function isType($type)
	{
		if (is_int($type)) {
			return $type === $this->type;
		} else {
			return (string) $type === token_name($this->type);
		}
	}

	public function isSingleValue($value)
	{
		return $this->type === NULL && $this->value === $value;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function setValue($value)
	{
		$this->value = $value;
	}

	public function __toString()
	{
		return $this->value;
	}

}
