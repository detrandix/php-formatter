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
			$this->rules[] = ['type', token_name($type), $use, $callback, $params];
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
			$this->rules[] = ['value', $value, $use, $callback, $params];
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

		$type = $token->getType();
		$value = $token->getValue();

		foreach ($this->rules as $rule) {
			$useTransformation =
				($rule[0] === 'type' && $type === $rule[1])
				|| ($rule[0] === 'value' && $type === NULL && $value === $rule[1]);

			if ($useTransformation) {
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
