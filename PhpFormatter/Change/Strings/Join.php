<?php

namespace PhpFormatter\Change\Strings;

use PhpFormatter\Change\IChange;
use PhpFormatter\Token;
use PhpFormatter\TokenQueue;

class Join implements IChange
{

	protected $setting;

	public function __construct($setting)
	{
		if (!($setting === NULL || in_array($setting, ['none', 'whitespace']))) {
			throw new \InvalidArgumentException("Unknown setting '{$setting}'.");
		}

		$this->setting = $setting;
	}

	public function canApply(Token $token, TokenQueue $queue)
	{
		return $token->isType(T_CONSTANT_ENCAPSED_STRING) && $queue->bottom()->isSingleValue('.');
	}

	/**
	 * @todo need improve
	 */
	public function apply(Token $token, TokenQueue $inputQueue, TokenQueue $outputQueue)
	{
		$outputQueue[] = $token;

		if ($this->setting === 'whitespace') {
			$outputQueue[] = new Token(' ', T_WHITESPACE);
			$outputQueue[] = $inputQueue->dequeue();
			$outputQueue[] = new Token(' ', T_WHITESPACE);
		}
	}

}
