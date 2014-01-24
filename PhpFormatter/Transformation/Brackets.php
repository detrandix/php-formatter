<?php

namespace PhpFormatter\Transformation;

use PhpFormatter\Token;
use PhpFormatter\TokenQueue;
use PhpFormatter\Formatter;

class Brackets implements ITransformation
{

	protected $setting;

	public function __construct($setting)
	{
		$this->setting = ['before' => NULL, 'inside' => NULL];

		foreach ((array) $setting as $key => $value) {
			if (!($value === NULL || in_array($value, ['none', 'whitespace']))) {
				throw new \InvalidArgumentException("Unknown setting '{$value}' for key '{$key}'.");
			}

			$this->setting[$key] = $value;
		}
	}

	public function canApply(Token $token, TokenQueue $queue)
	{
		return $token->isSingleValue('(') || ($token->isType(T_WHITESPACE) && $queue->count() > 0 && $queue->bottom()->isSingleValue('('));
	}

	public function transform(Token $token, TokenQueue $inputQueue, TokenQueue $outputQueue, Formatter $formatter)
	{
		if (
			$outputQueue->count() > 0
			&& (
				$outputQueue->top()->isType(T_IF)
				|| $outputQueue->top()->isType(T_FOR)
				|| $outputQueue->top()->isType(T_FOREACH)
				|| $outputQueue->top()->isType(T_WHILE)
				|| $outputQueue->top()->isType(T_SWITCH)
			)
		) {
			if ($this->setting['before'] === 'whitespace' && $token->isSingleValue('(')) {
				$outputQueue[] = new Token(' ', T_WHITESPACE);
			} elseif ($this->setting['before'] === 'none' && $token->isType(T_WHITESPACE)) {
				$token = $inputQueue->dequeue();
			} elseif ($token->isType(T_WHITESPACE)) {
				$outputQueue[] = $token;
				$token = $inputQueue->dequeue();
			}
		} else {
			if ($token->isType(T_WHITESPACE)) {
				$outputQueue[] = $token;
				$token = $inputQueue->dequeue();
			}
		}

		$bracketInnerQueue = new TokenQueue;
		$level = 1;
		do {
			$innerToken = $inputQueue->dequeue();

			if ($innerToken->isSingleValue('(')) {
				if ($level > 0) {
					$bracketInnerQueue[] = $innerToken;
				}

				$level++;
			} elseif ($innerToken->isSingleValue(')')) {
				if ($level > 1) {
					$bracketInnerQueue[] = $innerToken;
				}

				$level--;
			} else {
				$bracketInnerQueue[] = $innerToken;
			}
		} while ($level > 0);

		$outputQueue[] = '(';

		if ($this->setting['inside'] === 'whitespace' && !$bracketInnerQueue->bottom()->isType(T_WHITESPACE)) {
			$outputQueue[] = new Token(' ', T_WHITESPACE);
		} elseif ($this->setting['inside'] === 'none' && $bracketInnerQueue->bottom()->isType(T_WHITESPACE)) {
			$bracketInnerQueue->dequeue();
		}

		foreach ($formatter->processTokenQueue($bracketInnerQueue) as $processedToken) {
			$outputQueue[] = $processedToken;
		}

		if ($this->setting['inside'] === 'whitespace' && !$outputQueue->top()->isType(T_WHITESPACE)) {
			$outputQueue[] = new Token(' ', T_WHITESPACE);
		} elseif ($this->setting['inside'] === 'none' && $outputQueue->top()->isType(T_WHITESPACE)) {
			$outputQueue->pop();
		}

		$outputQueue[] = ')';
	}

}
