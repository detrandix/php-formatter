<?php

namespace PhpFormatter\Change;

use PhpFormatter\Token;
use PhpFormatter\TokenQueue;

class Constants implements IChange
{

	protected $setting;

	public function __construct($setting)
	{
		if (!($setting === NULL || in_array($setting, ['lowercase', 'uppercase']))) {
			throw new \InvalidArgumentException("Unknown setting '{$setting}'.");
		}

		$this->setting = $setting;
	}


	public function canApply(Token $token, TokenQueue $queue)
	{
		return $token->isType(T_STRING) && in_array(strtolower($token->getValue()), ['null', 'true', 'false']);
	}

	public function apply(Token $token, TokenQueue $inputQueue, TokenQueue $outputQueue)
	{
		if ($this->setting === 'lowercase') {
			$token->setValue(strtolower($token->getValue()));
		} elseif ($this->setting === 'uppercase') {
			$token->setValue(strtoupper($token->getValue()));
		}

		$outputQueue[] = $token;
	}

}
