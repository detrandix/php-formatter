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
		$this->setting = ['before' => NULL, 'after' => NULL, 'inside' => NULL];

		foreach ((array) $setting as $key => $value) {
			if (!($value === NULL || in_array($value, ['none', 'whitespace']))) {
				throw new \InvalidArgumentException("Unknown setting '{$value}' for key '{$key}'.");
			}

			$this->setting[$key] = $value;
		}
	}

	public function canApply(Token $token, TokenQueue $queue)
	{
		return $token->isSingleValue('(') || ($token->isType(T_WHITESPACE) && $queue->bottom()->isSingleValue('('));
	}

	/**
	 * @todo need improve
	 */
	public function transform(Token $token, TokenQueue $inputQueue, TokenQueue $outputQueue, Formatter $formatter)
	{
		if ($this->setting['before'] === 'whitespace' && $token->isSingleValue('(')) {
			$outputQueue[] = new Token(' ', T_WHITESPACE);
		} elseif ($this->setting['before'] === 'none' && $token->isType(T_WHITESPACE)) {
			$token = $inputQueue->dequeue();
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

		if ($this->setting['inside'] === 'whitespace') {
			$outputQueue[] = ' ';
		}

		foreach ($formatter->processTokenQueue($bracketInnerQueue) as $processedToken) {
			$outputQueue[] = $processedToken;
		}

		if ($this->setting['inside'] === 'whitespace') {
			$outputQueue[] = ' ';
		}

		$outputQueue[] = ')';
	}

}
