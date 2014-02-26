<?php

/**
 * This file is part of the PhpFormatter tool
 *
 * Copyright (c) 2014 Tomáš Lang
 *
 * For the full copyright and license information, please view the file
 * license-mit.txt that was distributed with this source code.
 */

namespace PhpFormatter;

class Token
{

	/** @var string */
	protected $value;

	/** @var mixed */
	protected $type;

	/**
	 * @param string $value
	 * @param mixed  $type
	 */
	public function __construct($value, $type = NULL)
	{
		$this->value = $value;
		$this->type = $type;
	}

	/**
	 * @param  mixed $zendToken
	 *
	 * @return self
	 */
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

	/**
	 * @param  int|string|null $type
	 *
	 * @return bool
	 */
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

	/**
	 * @param  array $types
	 *
	 * @return bool
	 */
	public function isInTypes(array $types)
	{
		foreach ($types as $type) {
			if ($this->isType($type)) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * @return string|null
	 */
	public function getType()
	{
		return $this->type ? token_name($this->type) : NULL;
	}

	/**
	 * @param  string $value
	 *
	 * @return bool
	 */
	public function isSingleValue($value)
	{
		return $this->type === NULL && $this->value === $value;
	}

	/**
	 * @param  array $values
	 *
	 * @return bool
	 */
	public function isInSingleValues(array $values)
	{
		if ($this->type !== NULL) {
			return FALSE;
		} else {
			return in_array($this->value, $values);
		}
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

	/**
	 * @param  Token $token
	 *
	 * @return bool
	 */
	public function isSame(Token $token)
	{
		return $this->getType() === $token->getType() && $this->getValue() == $token->getValue();
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->value;
	}

}
