<?php

namespace PhpFormatter\Transformation;

use PhpFormatter\Token;
use PhpFormatter\TokenList;
use PhpFormatter\Formatter;

class Constants implements ITransformation
{

	protected $setting;

	public function __construct($setting)
	{
		if (!($setting === NULL || in_array($setting, ['lowercase', 'uppercase']))) {
			throw new \InvalidArgumentException("Unknown setting '{$setting}'.");
		}

		$this->setting = $setting;
	}

	public function canApply(Token $token, TokenList $tokenList)
	{
		return $token->isType(T_STRING) && in_array(strtolower($token->getValue()), ['null', 'true', 'false']);
	}

	public function transform(Token $token, TokenList $inputTokenList, TokenList $outputTokenList, Formatter $formatter)
	{
		if ($this->setting === 'lowercase') {
			$token->setValue(strtolower($token->getValue()));
		} elseif ($this->setting === 'uppercase') {
			$token->setValue(strtoupper($token->getValue()));
		}

		$outputTokenList[] = $token;
	}

}
