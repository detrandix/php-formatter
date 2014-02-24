<?php

namespace PhpFormatter;

class ControlStructures
{

	protected $controls;

	public function __construct()
	{
		$this->controls = [];
	}

	public function registerToFormatter(Formatter $formatter)
	{
		$formatter->addTransformation(new Token('class', T_CLASS), [$this, 'addControl'], Formatter::USE_BEFORE);
		$formatter->addTransformation(new Token('if', T_IF), [$this, 'addControl'], Formatter::USE_BEFORE);
		$formatter->addTransformation(new Token('elseif', T_ELSEIF), [$this, 'addControl'], Formatter::USE_BEFORE);
		$formatter->addTransformation(new Token('else', T_ELSE), [$this, 'addControl'], Formatter::USE_BEFORE);
		$formatter->addTransformation(new Token('for', T_FOR), [$this, 'addControl'], Formatter::USE_BEFORE);
		$formatter->addTransformation(new Token('foreach', T_FOREACH), [$this, 'addControl'], Formatter::USE_BEFORE);
		$formatter->addTransformation(new Token('while', T_WHILE), [$this, 'addControl'], Formatter::USE_BEFORE);
		$formatter->addTransformation(new Token('do', T_DO), [$this, 'addControl'], Formatter::USE_BEFORE);
		$formatter->addTransformation(new Token('switch', T_SWITCH), [$this, 'addControl'], Formatter::USE_BEFORE);
		$formatter->addTransformation(new Token('try', T_TRY), [$this, 'addControl'], Formatter::USE_BEFORE);
		$formatter->addTransformation(new Token('catch', T_CATCH), [$this, 'addControl'], Formatter::USE_BEFORE);
		$formatter->addTransformation(new Token('function', T_FUNCTION), [$this, 'addControl'], Formatter::USE_BEFORE);

		$formatter->addTransformation(new Token('{'), [$this, 'addLeftBrace'], Formatter::USE_BEFORE);
		$formatter->addTransformation(new Token('}'), [$this, 'addRightBrace'], Formatter::USE_BEFORE);
	}

	public function addControl(Token $token)
	{
		// pokud je predchozi s FALSE, smazat
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
		array_pop($this->controls);
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

}
