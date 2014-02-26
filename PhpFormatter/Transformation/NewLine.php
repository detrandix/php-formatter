<?php

namespace PhpFormatter\Transformation;

use PhpFormatter\Indent;
use PhpFormatter\ControlStructures;
use PhpFormatter\Token;
use PhpFormatter\TokenList;
use PhpFormatter\TransformationRules;

class NewLine
{

	protected $controlStructures;

	protected $indent;

	public function __construct(ControlStructures $controlStructures, Indent $indent)
	{
		$this->controlStructures = $controlStructures;
		$this->indent = $indent;
	}

	public function register(TransformationRules $rules, $settings)
	{
		$rules->addRuleBySingleValue(';', TransformationRules::USE_AFTER, [$this, 'addNewLineAfterSemicolon']);

		if (isset($settings['new-line'])) {
			$newLineSettings = $settings['new-line'];

			if (isset($newLineSettings['else-elseif']) && $newLineSettings['else-elseif']) {
				$rules->addRuleByType([T_ELSEIF, T_ELSEIF], TransformationRules::USE_BEFORE, [$this, 'addNewLineBefore']);
			}

			if (isset($newLineSettings['while']) && $newLineSettings['while']) {
				$rules->addRuleByType(T_WHILE, TransformationRules::USE_BEFORE, [$this, 'addNewLineBeforeWhile']);
			}

			if (isset($newLineSettings['catch']) && $newLineSettings['catch']) {
				$rules->addRuleByType(T_CATCH, TransformationRules::USE_BEFORE, [$this, 'addNewLineBeforeWhile']);
			}
		}
	}

	public function addNewLineAfterSemicolon($token, $tokenList, $processedTokenList, $params)
	{
		if (!$this->controlStructures->isActualType(T_FOR)) {
			while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
				$processedTokenList->pop();
			}

			$processedTokenList[] = new Token("\n", T_WHITESPACE);
			$this->indent->addIndent($processedTokenList);
		}
	}

	public function addNewLineBefore($token, $tokenList, $processedTokenList, $params)
	{
		while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
			$processedTokenList->pop();
		}

		$processedTokenList[] = new Token("\n", T_WHITESPACE);
	}

	public function addNewLineBeforeWhile($token, $tokenList, $processedTokenList, $params)
	{
		if ($this->controlStructures->isLastType(T_DO)) {
			while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
				$processedTokenList->pop();
			}

			$processedTokenList[] = new Token("\n", T_WHITESPACE);
		}
	}

}
