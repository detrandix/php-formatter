<?php

namespace PhpFormatter\Transformation;

use PhpFormatter\Token;
use PhpFormatter\TokenList;
use PhpFormatter\Formatter;

class Operators
{

	public function registerToFormatter(Formatter $formatter, $settings)
	{
		if (!isset($settings['spaces']['arround-operators']))
			return FALSE;

		$settings = $settings['spaces']['arround-operators'];

		if (isset($settings['unary-operators']) && $settings['unary-operators']) {
			$this->register($formatter, new Token('++', T_INC));
			$this->register($formatter, new Token('--', T_DEC));
		}

		if (isset($settings['binary-operators']) && $settings['binary-operators']) {
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

		if (isset($settings['ternary-operators']) && $settings['ternary-operators']) {
			$this->register($formatter, new Token('?'));
			$this->register($formatter, new Token(':'));
		}

		if (isset($settings['string-concation-operator']) && $settings['string-concation-operator']) {
			$this->register($formatter, new Token('.'));
		}

		if (isset($settings['key-value-operator']) && $settings['key-value-operator']) {
			$this->register($formatter, new Token('=>', T_DOUBLE_ARROW));
		}

		if (isset($settings['assignment-operator']) && $settings['assignment-operator']) {
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

		if (isset($settings['object-operator']) && $settings['object-operator']) {
			$this->register($formatter, new Token('->', T_OBJECT_OPERATOR));
		}
	}

	protected function register(Formatter $formatter, Token $token)
	{
		$formatter->addTransformation($token, [$this, 'addWhitespace'], Formatter::USE_BEFORE);
		$formatter->addTransformation($token, [$this, 'addWhitespace'], Formatter::USE_AFTER);
	}

	public function addWhitespace($token, $tokenList, $processedTokenList)
	{
		if ($token->isSingleValue(':') && $processedTokenList->tail()->isType(T_WHITESPACE)) {
			$processedTokenList->pop();
		} else {
			$processedTokenList[] = new Token(' ', T_WHITESPACE);
		}
	}

}
