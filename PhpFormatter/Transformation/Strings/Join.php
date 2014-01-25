<?php

namespace PhpFormatter\Transformation\Strings;

use PhpFormatter\Transformation\ITransformation;
use PhpFormatter\Token;
use PhpFormatter\TokenList;
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

	public function canApply(Token $token, TokenList $tokenList)
	{
		return $token->isSingleValue('.');
	}

	public function transform(Token $token, TokenList $inputTokenList, TokenList $outputTokenList, Formatter $formatter)
	{
		if ($this->setting === 'none' || $this->setting === 'right') {
			if ($outputTokenList->tail()->isType(T_WHITESPACE)) {
				$outputTokenList->pop();
			}
		} elseif ($this->setting === 'whitespace' || $this->setting === 'left') {
			if (!$outputTokenList->tail()->isType(T_WHITESPACE)) {
				$outputTokenList[] = new Token(' ', T_WHITESPACE);
			}
		}

		$outputTokenList[] = $token;

		if ($this->setting === 'none' || $this->setting === 'left') {
			if ($inputTokenList->head()->isType(T_WHITESPACE)) {
				$inputTokenList->shift();
			}
		} elseif ($this->setting === 'whitespace' || $this->setting === 'right') {
			if (!$inputTokenList->head()->isType(T_WHITESPACE)) {
				$outputTokenList[] = new Token(' ', T_WHITESPACE);
			} else {
				$outputTokenList[] = $inputTokenList->shift();
			}
		}
	}

}
