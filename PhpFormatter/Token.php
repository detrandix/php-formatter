<?php

namespace PhpFormatter;

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
		if ($type === NULL) {
			return $this->type === $type;
		} elseif (is_int($type)) {
			return $type === $this->type;
		} else {
			return (string) $type === token_name($this->type);
		}
	}

	public function isInTypes($types)
	{
		foreach ($types as $type) {
			if ($this->isType($type)) {
				return TRUE;
			}
		}
		return FALSE;
	}

	public function getType()
	{
		return $this->type ? token_name($this->type) : NULL;
	}

	public function isSingleValue($value)
	{
		return $this->type === NULL && $this->value === $value;
	}

	public function isInSingleValues($values)
	{
		if ($this->type !== NULL) {
			return FALSE;
		} else {
			return in_array($this->value, $values);
		}
	}

	public function getValue()
	{
		return $this->value;
	}

	public function setValue($value)
	{
		$this->value = $value;
	}

	public function isSame(Token $token)
	{
		return $this->getType() === $token->getType() && $this->getValue() == $token->getValue();
	}

	public function __toString()
	{
		return $this->value;
	}

}
