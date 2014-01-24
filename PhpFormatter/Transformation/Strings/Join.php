<?php

namespace PhpFormatter\Transformation\Strings;

use PhpFormatter\Transformation\ITransformation;
use PhpFormatter\Token;
use PhpFormatter\TokenQueue;
use PhpFormatter\Formatter;

class Join implements ITransformation
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
	public function transform(Token $token, TokenQueue $inputQueue, TokenQueue $outputQueue, Formatter $formatter)
	{
		$outputQueue[] = $token;

		if ($this->setting === 'whitespace') {
			$outputQueue[] = new Token(' ', T_WHITESPACE);
			$outputQueue[] = $inputQueue->dequeue();
			$outputQueue[] = new Token(' ', T_WHITESPACE);
		}
	}

}
