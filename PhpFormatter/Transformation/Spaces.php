<?php

namespace PhpFormatter\Transformation;

use PhpFormatter\Indent;
use PhpFormatter\ControlStructures;
use PhpFormatter\Token;
use PhpFormatter\TokenList;
use PhpFormatter\Formatter;

class Spaces
{

	protected $controlStructures;

	protected $indent;

	public function __construct(ControlStructures $controlStructures, Indent $indent)
	{
		$this->controlStructures = $controlStructures;
		$this->indent = $indent;
	}

	public function registerToFormatter(Formatter $formatter, $settings)
	{
		$formatter->addTransformation(new Token('class', T_CLASS), [$this, 'addWhitespace'], Formatter::USE_AFTER);
		$formatter->addTransformation(new Token('function', T_FUNCTION), [$this, 'addWhitespace'], Formatter::USE_AFTER);
		$formatter->addTransformation(new Token('public', T_PUBLIC), [$this, 'addWhitespace'], Formatter::USE_AFTER);
		$formatter->addTransformation(new Token('protected', T_PROTECTED), [$this, 'addWhitespace'], Formatter::USE_AFTER);
		$formatter->addTransformation(new Token('private', T_PRIVATE), [$this, 'addWhitespace'], Formatter::USE_AFTER);
		$formatter->addTransformation(new Token('final', T_FINAL), [$this, 'addWhitespace'], Formatter::USE_AFTER);
		$formatter->addTransformation(new Token('print', T_PRINT), [$this, 'addWhitespace'], Formatter::USE_AFTER);
		$formatter->addTransformation(new Token('echo', T_ECHO), [$this, 'addWhitespace'], Formatter::USE_AFTER);
		$formatter->addTransformation(new Token('as', T_AS), [$this, 'addWhitespace'], Formatter::USE_BEFORE);
		$formatter->addTransformation(new Token('as', T_AS), [$this, 'addWhitespace'], Formatter::USE_AFTER);

		if (isset($settings['spaces']['before-keywords'])) {
			$beforeKeywordsSettings = $settings['spaces']['before-keywords'];

			if (isset($beforeKeywordsSettings['while']) && $beforeKeywordsSettings['while']) {
				$formatter->addTransformation(new Token('while', T_WHILE), [$this, 'addWhitespace'], Formatter::USE_BEFORE, [[], ['}']]);
			}

			if (isset($beforeKeywordsSettings['else-elseif']) && $beforeKeywordsSettings['else-elseif']) {
				$formatter->addTransformation(new Token('else', T_ELSE), [$this, 'addWhitespace'], Formatter::USE_BEFORE, [[], ['}']]);
				$formatter->addTransformation(new Token('elseif', T_ELSEIF), [$this, 'addWhitespace'], Formatter::USE_BEFORE, [[], ['}']]);
			}

			if (isset($beforeKeywordsSettings['catch']) && $beforeKeywordsSettings['catch']) {
				$formatter->addTransformation(new Token('catch', T_CATCH), [$this, 'addWhitespace'], Formatter::USE_BEFORE, [[], ['}']]);
			}
		}

		if (isset($settings['spaces']['arround-operators'])) {
			$operatorSettings = $settings['spaces']['arround-operators'];

			if (isset($operatorSettings['unary-operators']) && $operatorSettings['unary-operators']) {
				$this->register($formatter, new Token('++', T_INC));
				$this->register($formatter, new Token('--', T_DEC));
			}

			if (isset($operatorSettings['binary-operators']) && $operatorSettings['binary-operators']) {
				$this->register($formatter, new Token('&&', T_BOOLEAN_AND));
				$this->register($formatter, new Token('||', T_BOOLEAN_OR));
				$this->register($formatter, new Token('==', T_IS_EQUAL));
				$this->register($formatter, new Token('>=', T_IS_GREATER_OR_EQUAL));
				$this->register($formatter, new Token('===', T_IS_IDENTICAL));
				$this->register($formatter, new Token('!=', T_IS_NOT_EQUAL));
				$this->register($formatter, new Token('<>', T_IS_NOT_EQUAL));
				$this->register($formatter, new Token('!==', T_IS_NOT_IDENTICAL));
				$this->register($formatter, new Token('<=', T_IS_SMALLER_OR_EQUAL));
				$this->register($formatter, new Token('and', T_LOGICAL_AND));
				$this->register($formatter, new Token('or', T_LOGICAL_OR));
				$this->register($formatter, new Token('xor', T_LOGICAL_XOR));
				$this->register($formatter, new Token('<<', T_SL));
				$this->register($formatter, new Token('>>', T_SR));
			}

			if (isset($operatorSettings['ternary-operators']) && $operatorSettings['ternary-operators']) {
				$this->register($formatter, new Token('?'));
				$this->register($formatter, new Token(':'));
			}

			if (isset($operatorSettings['string-concation-operator']) && $operatorSettings['string-concation-operator']) {
				$this->register($formatter, new Token('.'));
			}

			if (isset($operatorSettings['key-value-operator']) && $operatorSettings['key-value-operator']) {
				$this->register($formatter, new Token('=>', T_DOUBLE_ARROW));
			}

			if (isset($operatorSettings['assignment-operator']) && $operatorSettings['assignment-operator']) {
				$this->register($formatter, new Token('='));
				$this->register($formatter, new Token('&=', T_AND_EQUAL));
				$this->register($formatter, new Token('.=', T_CONCAT_EQUAL));
				$this->register($formatter, new Token('/=', T_DIV_EQUAL));
				$this->register($formatter, new Token('-=', T_MINUS_EQUAL));
				$this->register($formatter, new Token('%=', T_MOD_EQUAL));
				$this->register($formatter, new Token('*=', T_MUL_EQUAL));
				$this->register($formatter, new Token('+=', T_PLUS_EQUAL));
				$this->register($formatter, new Token('<<=', T_SL_EQUAL));
				$this->register($formatter, new Token('>>=', T_SR_EQUAL));
				$this->register($formatter, new Token('^=', T_XOR_EQUAL));
			}

			if (isset($operatorSettings['object-operator']) && $operatorSettings['object-operator']) {
				$this->register($formatter, new Token('->', T_OBJECT_OPERATOR));
			}
		}

		if (isset($settings['spaces']['before-left-braces'])) {
			$beforeLeftBracesSettings = $settings['spaces']['before-left-braces'];

			if (isset($beforeLeftBracesSettings['class-declaration']) && $beforeLeftBracesSettings['class-declaration']) {
				$formatter->addTransformation(new Token('{'), [$this, 'addWhitespaceWithCondition'], Formatter::USE_BEFORE, T_CLASS);
			}

			if (isset($beforeLeftBracesSettings['method-declaration']) && $beforeLeftBracesSettings['method-declaration']) {
				$formatter->addTransformation(new Token('{'), [$this, 'addWhitespaceWithCondition'], Formatter::USE_BEFORE, T_FUNCTION);
			}

			if (isset($beforeLeftBracesSettings['if-elseif']) && $beforeLeftBracesSettings['if-elseif']) {
				$formatter->addTransformation(new Token('{'), [$this, 'addWhitespaceWithCondition'], Formatter::USE_BEFORE, T_IF);
				$formatter->addTransformation(new Token('{'), [$this, 'addWhitespaceWithCondition'], Formatter::USE_BEFORE, T_ELSEIF);
			}

			if (isset($beforeLeftBracesSettings['else']) && $beforeLeftBracesSettings['else']) {
				$formatter->addTransformation(new Token('{'), [$this, 'addWhitespaceWithCondition'], Formatter::USE_BEFORE, T_ELSE);
			}

			if (isset($beforeLeftBracesSettings['while']) && $beforeLeftBracesSettings['while']) {
				$formatter->addTransformation(new Token('{'), [$this, 'addWhitespaceWithCondition'], Formatter::USE_BEFORE, T_WHILE);
			}

			if (isset($beforeLeftBracesSettings['for-foreach']) && $beforeLeftBracesSettings['for-foreach']) {
				$formatter->addTransformation(new Token('{'), [$this, 'addWhitespaceWithCondition'], Formatter::USE_BEFORE, T_FOR);
				$formatter->addTransformation(new Token('{'), [$this, 'addWhitespaceWithCondition'], Formatter::USE_BEFORE, T_FOREACH);
			}

			if (isset($beforeLeftBracesSettings['do']) && $beforeLeftBracesSettings['do']) {
				$formatter->addTransformation(new Token('{'), [$this, 'addWhitespaceWithCondition'], Formatter::USE_BEFORE, T_DO);
			}

			if (isset($beforeLeftBracesSettings['switch']) && $beforeLeftBracesSettings['switch']) {
				$formatter->addTransformation(new Token('{'), [$this, 'addWhitespaceWithCondition'], Formatter::USE_BEFORE, T_SWITCH);
			}

			if (isset($beforeLeftBracesSettings['try']) && $beforeLeftBracesSettings['try']) {
				$formatter->addTransformation(new Token('{'), [$this, 'addWhitespaceWithCondition'], Formatter::USE_BEFORE, T_TRY);
			}

			if (isset($beforeLeftBracesSettings['catch']) && $beforeLeftBracesSettings['catch']) {
				$formatter->addTransformation(new Token('{'), [$this, 'addWhitespaceWithCondition'], Formatter::USE_BEFORE, T_CATCH);
			}
		}

		if (isset($settings['spaces']['before-parentheses'])) {
			$beforeParenthesesSettings = $settings['spaces']['before-parentheses'];

			if (isset($beforeParenthesesSettings['if-elseif']) && $beforeParenthesesSettings['if-elseif']) {
				$formatter->addTransformation(new Token('('), [$this, 'addWhitespace'], Formatter::USE_BEFORE, [[T_IF, T_ELSEIF], []]);
			}

			if (isset($beforeParenthesesSettings['for-foreach']) && $beforeParenthesesSettings['for-foreach']) {
				$formatter->addTransformation(new Token('('), [$this, 'addWhitespace'], Formatter::USE_BEFORE, [[T_FOR, T_FOREACH], []]);
			}

			if (isset($beforeParenthesesSettings['catch']) && $beforeParenthesesSettings['catch']) {
				$formatter->addTransformation(new Token('('), [$this, 'addWhitespace'], Formatter::USE_BEFORE, [[T_CATCH], []]);
			}

			if (isset($beforeParenthesesSettings['while']) && $beforeParenthesesSettings['while']) {
				$formatter->addTransformation(new Token('('), [$this, 'addWhitespace'], Formatter::USE_BEFORE, [[T_WHILE], []]);
			}

			if (isset($beforeParenthesesSettings['catch']) && $beforeParenthesesSettings['catch']) {
				$formatter->addTransformation(new Token('('), [$this, 'addWhitespace'], Formatter::USE_BEFORE, [[T_CATCH], []]);
			}

			if (isset($beforeParenthesesSettings['switch']) && $beforeParenthesesSettings['switch']) {
				$formatter->addTransformation(new Token('('), [$this, 'addWhitespace'], Formatter::USE_BEFORE, [[T_SWITCH], []]);
			}

			if (isset($beforeParenthesesSettings['array-declaration']) && $beforeParenthesesSettings['array-declaration']) {
				$formatter->addTransformation(new Token('('), [$this, 'addWhitespace'], Formatter::USE_BEFORE, [[T_ARRAY], []]);
			}
		}

		if (isset($settings['spaces']['other'])) {
			$otherSettings = $settings['spaces']['other'];

			if (isset($otherSettings['before-comma']) && $otherSettings['before-comma']) {
				$formatter->addTransformation(new Token(','), [$this, 'addWhitespace'], Formatter::USE_BEFORE);
			}

			if (isset($otherSettings['after-comma']) && $otherSettings['after-comma']) {
				$formatter->addTransformation(new Token(','), [$this, 'addWhitespace'], Formatter::USE_AFTER);
			}

			if (isset($otherSettings['before-semicolon']) && $otherSettings['before-semicolon']) {
				$formatter->addTransformation(new Token(';'), [$this, 'addWhitespace'], Formatter::USE_BEFORE);
			}

			if (isset($otherSettings['after-semicolon']) && $otherSettings['after-semicolon']) {
				$formatter->addTransformation(new Token(';'), [$this, 'addWhitespace'], Formatter::USE_AFTER);
			}

			if (isset($otherSettings['after-typecast']) && $otherSettings['after-typecast']) {
				$formatter->addTransformation(new Token('(array)', T_ARRAY_CAST), [$this, 'addWhitespace'], Formatter::USE_AFTER);
				$formatter->addTransformation(new Token('(bool)', T_BOOL_CAST), [$this, 'addWhitespace'], Formatter::USE_AFTER);
				$formatter->addTransformation(new Token('(boolean)', T_BOOL_CAST), [$this, 'addWhitespace'], Formatter::USE_AFTER);
				$formatter->addTransformation(new Token('(real)', T_DOUBLE_CAST), [$this, 'addWhitespace'], Formatter::USE_AFTER);
				$formatter->addTransformation(new Token('(double)', T_DOUBLE_CAST), [$this, 'addWhitespace'], Formatter::USE_AFTER);
				$formatter->addTransformation(new Token('(float)', T_DOUBLE_CAST), [$this, 'addWhitespace'], Formatter::USE_AFTER);
				$formatter->addTransformation(new Token('(int)', T_INT_CAST), [$this, 'addWhitespace'], Formatter::USE_AFTER);
				$formatter->addTransformation(new Token('(integer)', T_INT_CAST), [$this, 'addWhitespace'], Formatter::USE_AFTER);
				$formatter->addTransformation(new Token('(object)', T_OBJECT_CAST), [$this, 'addWhitespace'], Formatter::USE_AFTER);
				$formatter->addTransformation(new Token('(string)', T_STRING_CAST), [$this, 'addWhitespace'], Formatter::USE_AFTER);
			}
		}
	}

	protected function register(Formatter $formatter, Token $token)
	{
		$formatter->addTransformation($token, [$this, 'addWhitespace'], Formatter::USE_BEFORE);
		$formatter->addTransformation($token, [$this, 'addWhitespace'], Formatter::USE_AFTER);
	}

	public function addWhitespace($token, $tokenList, $processedTokenList, $params)
	{
		$addWhitespace = TRUE;

		if ($params !== NULL) {
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

	public function addWhitespaceWithCondition($token, $tokenList, $processedTokenList, $params)
	{
		if ($this->controlStructures->isActualType($params)) {
			$processedTokenList[] = new Token(' ', T_WHITESPACE);
		}
	}

}
