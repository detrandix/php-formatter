<?php

namespace PhpFormatter\Transformation\Strings;

use PhpFormatter\Transformation\ITransformation;
use PhpFormatter\Token;
use PhpFormatter\TokenList;
use PhpFormatter\Formatter;

class Semicolon implements ITransformation
{

	protected $setting;

	public function __construct($setting)
	{
		if (!($setting === NULL || in_array($setting, ['newline']))) {
			throw new \InvalidArgumentException("Unknown setting '{$setting}' in String\Semicolon transformation.");
		}

		$this->setting = $setting;
	}

	public function canApply(Token $token, TokenList $tokenList)
	{
		return $token->isSingleValue(';');
	}

	public function transform(Token $token, TokenList $inputTokenList, TokenList $outputTokenList, Formatter $formatter)
	{
		$outputTokenList[] = $token;

		if ($this->setting === 'newline') {
			if ($inputTokenList->count() && $inputTokenList->head()->isType(T_WHITESPACE)) {
				$token = $inputTokenList->shift();

				if (!preg_match('/^\\n/m', $token->getValue())) {
					$token->setValue("\n" . $token->getValue());
				}

				$outputTokenList[] = $token;
			} else {
				$outputTokenList[] = new Token("\n", T_WHITESPACE);
			}
		}
	}

}
