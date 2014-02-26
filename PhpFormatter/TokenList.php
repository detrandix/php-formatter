<?php

/**
 * This file is part of the PhpFormatter tool
 *
 * Copyright (c) 2014 Tomáš Lang
 */

namespace PhpFormatter;

use ArrayIterator;

class TokenList implements \ArrayAccess, \IteratorAggregate, \Countable
{

	/** @var Token[] */
	protected $tokens = [];

	/**
	 * @param Token[] $tokens
	 */
	public function __construct($tokens = [])
	{
		foreach ($tokens as $token) {
			$this->push($token);
		}
	}

	/**
	 * @param Token|array|string $token
	 */
	public function push($token)
	{
		$this->tokens[] = $token instanceof Token ? $token : Token::createFromZendToken($token);
	}

	/**
	 * @return Token|NULL
	 */
	public function pop()
	{
		return array_pop($this->tokens);
	}

	/**
	 * @param Token|array|string $token
	 */
	public function unshift($token)
	{
		array_unshift($this->tokens, $token instanceof Token ? $token : Token::createFromZendToken($token));
	}

	/**
	 * @return Token|NULL
	 */
	public function shift()
	{
		return array_shift($this->tokens);
	}

	/**
	 * @return Token|NULL
	 */
	public function head()
	{
		return reset($this->tokens) ?: NULL;
	}

	/**
	 * @return Token|NULL
	 */
	public function tail()
	{
		return end($this->tokens) ?: NULL;
	}

	/**
	 * @return bool
	 */
	public function isEmpty()
	{
		return count($this->tokens) === 0;
	}

	/**
	 * @param  mixed $offset
	 *
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return FALSE;
	}

	/**
	 * @param  mixed $offset
	 *
	 * @return NULL
	 */
	public function offsetGet($offset)
	{
		return NULL;
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		$this->push($value);
	}

	/**
	 * @param  mixed $offset
	 *
	 * @return NULL
	 */
	public function offsetUnset($offset)
	{
	}

	/**
	 * @return ArrayIterator
	 */
    public function getIterator()
    {
        return new ArrayIterator($this->tokens);
    }

	/**
	 * @return int
	 */
	public function count()
	{
		return count($this->tokens);
	}

}
