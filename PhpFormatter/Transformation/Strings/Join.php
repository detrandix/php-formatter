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
		if (!($setting === NULL || in_array($setting, ['none', 'whitespace', 'left', 'right']))) {
			throw new \InvalidArgumentException("Unknown setting '{$setting}'.");
		}

		$this->setting = $setting;
	}

	public function canApply(Token $token, TokenQueue $queue)
	{
		return $token->isSingleValue('.');
	}

	public function transform(Token $token, TokenQueue $inputQueue, TokenQueue $outputQueue, Formatter $formatter)
	{
		if ($this->setting === 'none' || $this->setting === 'right') {
			if ($outputQueue->top()->isType(T_WHITESPACE)) {
				$outputQueue->pop();
			}
		} elseif ($this->setting === 'whitespace' || $this->setting === 'left') {
			if (!$outputQueue->top()->isType(T_WHITESPACE)) {
				$outputQueue[] = new Token(' ', T_WHITESPACE);
			}
		}

		$outputQueue[] = $token;

		if ($this->setting === 'none' || $this->setting === 'left') {
			if ($inputQueue->bottom()->isType(T_WHITESPACE)) {
				$inputQueue->dequeue();
			}
		} elseif ($this->setting === 'whitespace' || $this->setting === 'right') {
			if (!$inputQueue->bottom()->isType(T_WHITESPACE)) {
				$outputQueue[] = new Token(' ', T_WHITESPACE);
			} else {
				$outputQueue[] = $inputQueue->dequeue();
			}
		}
	}

}
