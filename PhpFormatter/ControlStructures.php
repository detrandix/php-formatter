<?php

namespace PhpFormatter;

class ControlStructures
{

	protected $controls;

	protected $lastPopped;

	public function __construct()
	{
		$this->controls = [];
		$this->lastPopped = NULL;
	}

	public function register(TransformationRules $rules)
	{
		$controls = [
			T_CLASS, T_IF, T_ELSEIF, T_ELSE, T_FOR, T_FOREACH,
			T_WHILE, T_DO, T_SWITCH, T_TRY, T_CATCH, T_FUNCTION
		];
		$rules->addRuleByType($controls, TransformationRules::USE_BEFORE, [$this, 'addControl']);

		$rules->addRuleBySingleValue('{', TransformationRules::USE_BEFORE, [$this, 'addLeftBrace']);
		$rules->addRuleBySingleValue('}', TransformationRules::USE_AFTER, [$this, 'addRightBrace']);
		$rules->addRuleBySingleValue(';', TransformationRules::USE_AFTER, [$this, 'addSemicolon']);
	}

	public function addControl(Token $token)
	{
		$this->controls[] = [$token, FALSE];
	}

	public function addLeftBrace()
	{
		if (count($this->controls)) {
			$lastIndex = count($this->controls) - 1;
			if ($this->controls[$lastIndex][1] === FALSE) {
				$this->controls[$lastIndex][1] = TRUE;
			} else {
				$this->controls[] = [NULL, FALSE];
			}
		} else {
			$this->controls[] = [NULL, FALSE];
		}
	}

	public function addRightBrace()
	{
		$this->lastPopped = array_pop($this->controls);
	}

	public function addSemicolon()
	{
		if (count($this->controls) && !$this->isActualType(T_FOR)) {
			$lastIndex = count($this->controls) - 1;
			if ($this->controls[$lastIndex][1] === FALSE) {
				$this->addRightBrace();
			}
		}
	}

	public function isActualType($type)
	{
		if (count($this->controls) === 0)
			return FALSE;

		$lastIndex = count($this->controls) - 1;

		if ($this->controls[$lastIndex][0] === NULL)
			return FALSE;

		return $this->controls[$lastIndex][0]->isType($type);
	}

	public function isLastType($type)
	{
		if ($this->lastPopped === NULL || $this->lastPopped[0] === NULL) {
			return FALSE;
		}

		return $this->lastPopped[0]->isType($type);
	}

}
