<?php

namespace PhpFormatter;

class TransformationRules
{

	const USE_BEFORE = 1;
	const USE_AFTER = 2;

	protected $rules;

	public function __construct()
	{
		$this->rules = [];
	}

	public function addRuleByType($types, $use, $callback)
	{
		foreach ((array) $types as $type) {
			$this->rules[] = ['type', token_name($type), $use, $callback];
		}
	}

	public function addRuleBySingleValue($values, $use, $callback)
	{
		foreach ((array) $values as $value) {
			$this->rules[] = ['value', $value, $use, $callback];
		}
	}

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
					$transformations[self::USE_BEFORE][] = $rule[3];
				}

				if ($this->hasFlag($rule[2], self::USE_AFTER)) {
					$transformations[self::USE_AFTER][] = $rule[3];
				}
			}
		}

		return $transformations;
	}

	protected function hasFlag($value, $flag)
	{
		return ($value & $flag) === $flag;
	}

}
