<?php

namespace PhpFormatter\Transformation;

use PhpFormatter\Token;
use PhpFormatter\TokenList;
use PhpFormatter\Formatter;

interface ITransformation
{

	public function canApply(Token $token, TokenList $tokenList);

	public function transform(Token $token, TokenList $inputTokenList, TokenList $outputTokenList, Formatter $formatter);

}