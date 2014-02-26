<?php

namespace PhpFormatter\Transformation;

use PhpFormatter\Indent;
use PhpFormatter\ControlStructures;
use PhpFormatter\Token;
use PhpFormatter\TokenList;
use PhpFormatter\TransformationRules;

class Spaces
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
		$typesWithSpaceAfter = [
			T_CLASS, T_FUNCTION, T_PUBLIC, T_PROTECTED, T_PRIVATE,
			T_FINAL, T_PRINT, T_ECHO
		];
		$rules->addRuleByType($typesWithSpaceAfter, TransformationRules::USE_AFTER, [$this, 'addWhitespace']);
		$rules->addRuleByType(T_AS, TransformationRules::USE_BEFORE | TransformationRules::USE_AFTER, [$this, 'addWhitespace']);

		if (isset($settings['spaces']['before-keywords'])) {
			$beforeKeywordsSettings = $settings['spaces']['before-keywords'];

			if (isset($beforeKeywordsSettings['while']) && $beforeKeywordsSettings['while']) {
				$rules->addRuleByType(T_WHILE, TransformationRules::USE_BEFORE, [$this, 'addWhitespace'], [[], ['}']]);
			}

			if (isset($beforeKeywordsSettings['else-elseif']) && $beforeKeywordsSettings['else-elseif']) {
				$rules->addRuleByType([T_ELSE, T_ELSEIF], TransformationRules::USE_BEFORE, [$this, 'addWhitespace'], [[], ['}']]);
			}

			if (isset($beforeKeywordsSettings['catch']) && $beforeKeywordsSettings['catch']) {
				$rules->addRuleByType(T_CATCH, TransformationRules::USE_BEFORE, [$this, 'addWhitespace'], [[], ['}']]);
			}
		}

		if (isset($settings['spaces']['arround-operators'])) {
			$operatorSettings = $settings['spaces']['arround-operators'];

			if (isset($operatorSettings['unary-operators']) && $operatorSettings['unary-operators']) {
				$rules->addRuleByType([T_INC, T_DEC], TransformationRules::USE_BEFORE | TransformationRules::USE_AFTER, [$this, 'addWhitespace']);
			}

			if (isset($operatorSettings['binary-operators']) && $operatorSettings['binary-operators']) {
				$binaryOperators = [
					T_BOOLEAN_AND, T_BOOLEAN_OR, T_IS_EQUAL, T_IS_GREATER_OR_EQUAL,
					T_IS_IDENTICAL, T_IS_NOT_EQUAL, T_IS_NOT_IDENTICAL,
					T_IS_SMALLER_OR_EQUAL, T_LOGICAL_AND, T_LOGICAL_OR,
					T_LOGICAL_XOR, T_SL, T_SR
				];
				$rules->addRuleByType($binaryOperators, TransformationRules::USE_BEFORE | TransformationRules::USE_AFTER, [$this, 'addWhitespace']);
			}

			if (isset($operatorSettings['ternary-operators']) && $operatorSettings['ternary-operators']) {
				$rules->addRuleBySingleValue(['?', ':'], TransformationRules::USE_BEFORE | TransformationRules::USE_AFTER, [$this, 'addWhitespace']);
			}

			if (isset($operatorSettings['string-concation-operator']) && $operatorSettings['string-concation-operator']) {
				$rules->addRuleBySingleValue('.', TransformationRules::USE_BEFORE | TransformationRules::USE_AFTER, [$this, 'addWhitespace']);
			}

			if (isset($operatorSettings['key-value-operator']) && $operatorSettings['key-value-operator']) {
				$rules->addRuleByType(T_DOUBLE_ARROW, TransformationRules::USE_BEFORE | TransformationRules::USE_AFTER, [$this, 'addWhitespace']);
			}

			if (isset($operatorSettings['assignment-operator']) && $operatorSettings['assignment-operator']) {
				$assignmentOperators = [
					T_AND_EQUAL, T_CONCAT_EQUAL, T_DIV_EQUAL, T_MINUS_EQUAL,
					T_MOD_EQUAL, T_MUL_EQUAL, T_PLUS_EQUAL, T_SL_EQUAL,
					T_SR_EQUAL, T_XOR_EQUAL
				];
				$rules->addRuleByType($assignmentOperators, TransformationRules::USE_BEFORE | TransformationRules::USE_AFTER, [$this, 'addWhitespace']);
				$rules->addRuleBySingleValue('=', TransformationRules::USE_BEFORE | TransformationRules::USE_AFTER, [$this, 'addWhitespace']);
			}

			if (isset($operatorSettings['object-operator']) && $operatorSettings['object-operator']) {
				$rules->addRuleByType(T_OBJECT_OPERATOR, TransformationRules::USE_BEFORE | TransformationRules::USE_AFTER, [$this, 'addWhitespace']);
			}
		}

		if (isset($settings['spaces']['before-left-braces'])) {
			$beforeLeftBracesSettings = $settings['spaces']['before-left-braces'];

			if (isset($beforeLeftBracesSettings['class-declaration']) && $beforeLeftBracesSettings['class-declaration']) {
				$rules->addRuleBySingleValue('{', TransformationRules::USE_BEFORE, [$this, 'addWhitespaceWithCondition'], T_CLASS);
			}

			if (isset($beforeLeftBracesSettings['method-declaration']) && $beforeLeftBracesSettings['method-declaration']) {
				$rules->addRuleBySingleValue('{', TransformationRules::USE_BEFORE, [$this, 'addWhitespaceWithCondition'], T_FUNCTION);
			}

			if (isset($beforeLeftBracesSettings['if-elseif']) && $beforeLeftBracesSettings['if-elseif']) {
				$rules->addRuleBySingleValue('{', TransformationRules::USE_BEFORE, [$this, 'addWhitespaceWithCondition'], T_IF);
				$rules->addRuleBySingleValue('{', TransformationRules::USE_BEFORE, [$this, 'addWhitespaceWithCondition'], T_ELSEIF);
			}

			if (isset($beforeLeftBracesSettings['else']) && $beforeLeftBracesSettings['else']) {
				$rules->addRuleBySingleValue('{', TransformationRules::USE_BEFORE, [$this, 'addWhitespaceWithCondition'], T_ELSE);
			}

			if (isset($beforeLeftBracesSettings['while']) && $beforeLeftBracesSettings['while']) {
				$rules->addRuleBySingleValue('{', TransformationRules::USE_BEFORE, [$this, 'addWhitespaceWithCondition'], T_WHILE);
			}

			if (isset($beforeLeftBracesSettings['for-foreach']) && $beforeLeftBracesSettings['for-foreach']) {
				$rules->addRuleBySingleValue('{', TransformationRules::USE_BEFORE, [$this, 'addWhitespaceWithCondition'], T_FOR);
				$rules->addRuleBySingleValue('{', TransformationRules::USE_BEFORE, [$this, 'addWhitespaceWithCondition'], T_FOREACH);
			}

			if (isset($beforeLeftBracesSettings['do']) && $beforeLeftBracesSettings['do']) {
				$rules->addRuleBySingleValue('{', TransformationRules::USE_BEFORE, [$this, 'addWhitespaceWithCondition'], T_DO);
			}

			if (isset($beforeLeftBracesSettings['switch']) && $beforeLeftBracesSettings['switch']) {
				$rules->addRuleBySingleValue('{', TransformationRules::USE_BEFORE, [$this, 'addWhitespaceWithCondition'], T_SWITCH);
			}

			if (isset($beforeLeftBracesSettings['try']) && $beforeLeftBracesSettings['try']) {
				$rules->addRuleBySingleValue('{', TransformationRules::USE_BEFORE, [$this, 'addWhitespaceWithCondition'], T_TRY);
			}

			if (isset($beforeLeftBracesSettings['catch']) && $beforeLeftBracesSettings['catch']) {
				$rules->addRuleBySingleValue('{', TransformationRules::USE_BEFORE, [$this, 'addWhitespaceWithCondition'], T_CATCH);
			}
		}

		if (isset($settings['spaces']['before-parentheses'])) {
			$beforeParenthesesSettings = $settings['spaces']['before-parentheses'];

			if (isset($beforeParenthesesSettings['if-elseif']) && $beforeParenthesesSettings['if-elseif']) {
				$rules->addRuleBySingleValue('(', TransformationRules::USE_BEFORE, [$this, 'addWhitespace'], [[T_IF, T_ELSEIF], []]);
			}

			if (isset($beforeParenthesesSettings['for-foreach']) && $beforeParenthesesSettings['for-foreach']) {
				$rules->addRuleBySingleValue('(', TransformationRules::USE_BEFORE, [$this, 'addWhitespace'], [[T_FOR, T_FOREACH], []]);
			}

			if (isset($beforeParenthesesSettings['catch']) && $beforeParenthesesSettings['catch']) {
				$rules->addRuleBySingleValue('(', TransformationRules::USE_BEFORE, [$this, 'addWhitespace'], [[T_CATCH], []]);
			}

			if (isset($beforeParenthesesSettings['while']) && $beforeParenthesesSettings['while']) {
				$rules->addRuleBySingleValue('(', TransformationRules::USE_BEFORE, [$this, 'addWhitespace'], [[T_WHILE], []]);
			}

			if (isset($beforeParenthesesSettings['catch']) && $beforeParenthesesSettings['catch']) {
				$rules->addRuleBySingleValue('(', TransformationRules::USE_BEFORE, [$this, 'addWhitespace'], [[T_CATCH], []]);
			}

			if (isset($beforeParenthesesSettings['switch']) && $beforeParenthesesSettings['switch']) {
				$rules->addRuleBySingleValue('(', TransformationRules::USE_BEFORE, [$this, 'addWhitespace'], [[T_SWITCH], []]);
			}

			if (isset($beforeParenthesesSettings['array-declaration']) && $beforeParenthesesSettings['array-declaration']) {
				$rules->addRuleBySingleValue('(', TransformationRules::USE_BEFORE, [$this, 'addWhitespace'], [[T_ARRAY], []]);
			}
		}

		if (isset($settings['spaces']['other'])) {
			$otherSettings = $settings['spaces']['other'];

			if (isset($otherSettings['before-comma']) && $otherSettings['before-comma']) {
				$rules->addRuleBySingleValue(',', TransformationRules::USE_BEFORE, [$this, 'addWhitespace']);
			}

			if (isset($otherSettings['after-comma']) && $otherSettings['after-comma']) {
				$rules->addRuleBySingleValue(',', TransformationRules::USE_AFTER, [$this, 'addWhitespace']);
			}

			if (isset($otherSettings['before-semicolon']) && $otherSettings['before-semicolon']) {
				$rules->addRuleBySingleValue(';', TransformationRules::USE_BEFORE, [$this, 'addWhitespace']);
			}

			if (isset($otherSettings['after-semicolon']) && $otherSettings['after-semicolon']) {
				$rules->addRuleBySingleValue(';', TransformationRules::USE_AFTER, [$this, 'addWhitespace']);
			}

			if (isset($otherSettings['after-typecast']) && $otherSettings['after-typecast']) {
				$typecasts = [
					T_ARRAY_CAST, T_BOOL_CAST, T_DOUBLE_CAST,
					T_INT_CAST, T_OBJECT_CAST, T_STRING_CAST
				];
				$rules->addRuleByType($typecasts, TransformationRules::USE_AFTER, [$this, 'addWhitespace']);
			}
		}
	}

	/**
	 * @param Token      $token
	 * @param TokenList  $tokenList
	 * @param TokenList  $processedTokenList
	 * @param array|null $params
	 */
	public function addWhitespace(Token $token, TokenList $tokenList, TokenList $processedTokenList, $params)
	{
		$addWhitespace = TRUE;

		if ($params !== NULL) {
			// @todo muze to tady byt obecne?
			while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
				$processedTokenList->pop();
			}

			$addWhitespace =
				$processedTokenList->tail()->isInTypes($params[0])
				|| $processedTokenList->tail()->isInSingleValues($params[1]);
		} elseif ($token->isSingleValue(':') && $processedTokenList->tail()->isType(T_WHITESPACE)) {
			$processedTokenList->pop();
			$addWhitespace = FALSE;
		}

		if ($addWhitespace) {
			$processedTokenList[] = new Token(' ', T_WHITESPACE);
		}
	}

	/**
	 * @param Token     $token
	 * @param TokenList $tokenList
	 * @param TokenList $processedTokenList
	 * @param int       $params
	 */
	public function addWhitespaceWithCondition(Token $token, TokenList $tokenList, TokenList $processedTokenList, $params)
	{
		if ($this->controlStructures->isActualType($params)) {
			$processedTokenList[] = new Token(' ', T_WHITESPACE);
		}
	}

}
