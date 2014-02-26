<?php

namespace PhpFormatter;

class TransformationRules
{

	const USE_BEFORE = 1;
	const USE_AFTER = 2;

	/** @var array */
	protected $rules;

	public function __construct()
	{
		$this->rules = [];
	}

	/**
	 * @param int|int[] $types
	 * @param int       $use
	 * @param callback  $callback
	 * @param mixed     $params
	 */
	public function addRuleByType($types, $use, $callback, $params = NULL)
	{
		foreach ((array) $types as $type) {
			if (!isset($this->rules[token_name($type)])) {
				$this->rules[token_name($type)] = [];
			}

			$this->rules[token_name($type)][] = ['type', token_name($type), $use, $callback, $params];
		}
	}

	/**
	 * @param string|string[] $values
	 * @param int             $use
	 * @param callback        $callback
	 * @param mixed           $params
	 */
	public function addRuleBySingleValue($values, $use, $callback, $params = NULL)
	{
		foreach ((array) $values as $value) {
			if (!isset($this->rules[$value])) {
				$this->rules[$value] = [];
			}

			$this->rules[$value][] = ['value', $value, $use, $callback, $params];
		}
	}

	/**
	 * @param  Token $token
	 *
	 * @return array
	 */
	public function getTransformations(Token $token)
	{
		$transformations = [
			self::USE_BEFORE => [],
			self::USE_AFTER => []
		];

		$key = $token->getType() ?: $token->getValue();

		if (isset($this->rules[$key])) {
			foreach ($this->rules[$key] as $rule) {
				if ($this->hasFlag($rule[2], self::USE_BEFORE)) {
					$transformations[self::USE_BEFORE][] = [$rule[3], $rule[4]];
				}

				if ($this->hasFlag($rule[2], self::USE_AFTER)) {
					$transformations[self::USE_AFTER][] = [$rule[3], $rule[4]];
				}
			}
		}

		return $transformations;
	}

	/**
	 * @param  int $value
	 * @param  int $flag
	 *
	 * @return bool
	 */
	protected function hasFlag($value, $flag)
	{
		return ($value & $flag) === $flag;
	}

}
