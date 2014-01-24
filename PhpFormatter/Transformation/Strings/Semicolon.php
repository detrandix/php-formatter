<?php

namespace PhpFormatter\Transformation\Strings;

use PhpFormatter\Transformation\ITransformation;
use PhpFormatter\Token;
use PhpFormatter\TokenQueue;
use PhpFormatter\Formatter;

class Semicolon implements ITransformation
{

	protected $setting;

	public function __construct($setting)
	{
		if (!($setting === NULL || in_array($setting, ['newline']))) {
			throw new \InvalidArgumentException("Unknown setting '{$setting}'.");
		}

		$this->setting = $setting;
	}

	public function canApply(Token $token, TokenQueue $queue)
	{
		return $token->isSingleValue(';');
	}

	public function transform(Token $token, TokenQueue $inputQueue, TokenQueue $outputQueue, Formatter $formatter)
	{
		$outputQueue[] = $token;

		if ($this->setting === 'newline') {
			if ($inputQueue->count() && $inputQueue->bottom()->isType(T_WHITESPACE)) {
				$token = $inputQueue->dequeue();

				if (!preg_match('/^\\n/m', $token->getValue())) {
					$token->setValue("\n" . $token->getValue());
				}

				$outputQueue[] = $token;
			} else {
				$outputQueue[] = new Token("\n", T_WHITESPACE);
			}
		}
	}

}
