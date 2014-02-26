<?php

namespace PhpFormatter\Transformation;

use PhpFormatter\Indent;
use PhpFormatter\ControlStructures;
use PhpFormatter\Token;
use PhpFormatter\TokenList;
use PhpFormatter\TransformationRules;

class NewLine
{

	/** @var ControlStructures */
	protected $controlStructures;

	/** @var Indent */
	protected $indent;

	/**
	 * @param ControlStructures $controlStructures
	 * @param Indent            $indent
	 */
	public function __construct(ControlStructures $controlStructures, Indent $indent)
	{
		$this->controlStructures = $controlStructures;
		$this->indent = $indent;
	}

	/**
	 * @param TransformationRules $rules
	 * @param array               $settings
	 */
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

	/**
	 * @param Token     $token
	 * @param TokenList $tokenList
	 * @param TokenList $processedTokenList
	 */
	public function addNewLineAfterSemicolon(Token $token, TokenList $tokenList, TokenList $processedTokenList)
	{
		if (!$this->controlStructures->isActualType(T_FOR)) {
			while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
				$processedTokenList->pop();
			}

			$processedTokenList[] = new Token("\n", T_WHITESPACE);
			$this->indent->addIndent($processedTokenList);
		}
	}

	/**
	 * @param Token     $token
	 * @param TokenList $tokenList
	 * @param TokenList $processedTokenList
	 */
	public function addNewLineBefore(Token $token, TokenList $tokenList, TokenList $processedTokenList)
	{
		while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
			$processedTokenList->pop();
		}

		$processedTokenList[] = new Token("\n", T_WHITESPACE);
	}

	/**
	 * @param Token     $token
	 * @param TokenList $tokenList
	 * @param TokenList $processedTokenList
	 */
	public function addNewLineBeforeWhile(Token $token, TokenList $tokenList, TokenList $processedTokenList)
	{
		if ($this->controlStructures->isLastType(T_DO)) {
			while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
				$processedTokenList->pop();
			}

			$processedTokenList[] = new Token("\n", T_WHITESPACE);
		}
	}

}
