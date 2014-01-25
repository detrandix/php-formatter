<?php

namespace PhpFormatter\Transformation;

use PhpFormatter\Token;
use PhpFormatter\TokenList;
use PhpFormatter\Formatter;

class Brackets implements ITransformation
{

	protected $setting;

	public function __construct($setting)
	{
		$this->setting = ['before' => NULL, 'inside' => NULL];

		foreach ((array) $setting as $key => $value) {
			if (!($value === NULL || in_array($value, ['none', 'whitespace']))) {
				throw new \InvalidArgumentException("Unknown setting '{$value}' for key '{$key}' in Brackets transformation.");
			}

			$this->setting[$key] = $value;
		}
	}

	public function canApply(Token $token, TokenList $tokenList)
	{
		return $token->isSingleValue('(') || ($token->isType(T_WHITESPACE) && $tokenList->count() > 0 && $tokenList->head()->isSingleValue('('));
	}

	public function transform(Token $token, TokenList $inputTokenList, TokenList $outputTokenList, Formatter $formatter)
	{
		if (
			$outputTokenList->count() > 0
			&& (
				$outputTokenList->tail()->isType(T_IF)
				|| $outputTokenList->tail()->isType(T_FOR)
				|| $outputTokenList->tail()->isType(T_FOREACH)
				|| $outputTokenList->tail()->isType(T_WHILE)
				|| $outputTokenList->tail()->isType(T_SWITCH)
			)
		) {
			if ($this->setting['before'] === 'whitespace' && $token->isSingleValue('(')) {
				$outputTokenList[] = new Token(' ', T_WHITESPACE);
			} elseif ($this->setting['before'] === 'none' && $token->isType(T_WHITESPACE)) {
				$token = $inputTokenList->shift();
			} elseif ($token->isType(T_WHITESPACE)) {
				$outputTokenList[] = $token;
				$token = $inputTokenList->shift();
			}
		} else {
			if ($token->isType(T_WHITESPACE)) {
				$outputTokenList[] = $token;
				$token = $inputTokenList->shift();
			}
		}

		$bracketInnerTokenList = new TokenList;
		$level = 1;
		do {
			$innerToken = $inputTokenList->shift();

			if ($innerToken->isSingleValue('(')) {
				if ($level > 0) {
					$bracketInnerTokenList[] = $innerToken;
				}

				$level++;
			} elseif ($innerToken->isSingleValue(')')) {
				if ($level > 1) {
					$bracketInnerTokenList[] = $innerToken;
				}

				$level--;
			} else {
				$bracketInnerTokenList[] = $innerToken;
			}
		} while ($level > 0);

		$outputTokenList[] = '(';

		if ($this->setting['inside'] === 'whitespace' && !$bracketInnerTokenList->isEmpty() && !$bracketInnerTokenList->head()->isType(T_WHITESPACE)) {
			$outputTokenList[] = new Token(' ', T_WHITESPACE);
		} elseif ($this->setting['inside'] === 'none' && !$bracketInnerTokenList->isEmpty() && $bracketInnerTokenList->head()->isType(T_WHITESPACE)) {
			$bracketInnerTokenList->shift();
		}

		foreach ($formatter->processTokenList($bracketInnerTokenList) as $processedToken) {
			$outputTokenList[] = $processedToken;
		}

		if ($this->setting['inside'] === 'whitespace' && !$outputTokenList->tail()->isType(T_WHITESPACE) && !$outputTokenList->tail()->isSingleValue('(')) {
			$outputTokenList[] = new Token(' ', T_WHITESPACE);
		} elseif ($this->setting['inside'] === 'none' && $outputTokenList->tail()->isType(T_WHITESPACE)) {
			$outputTokenList->pop();
		}

		$outputTokenList[] = ')';
	}

}
