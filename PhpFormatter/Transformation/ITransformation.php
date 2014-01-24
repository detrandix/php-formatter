<?php

namespace PhpFormatter\Transformation;

use PhpFormatter\Token;
use PhpFormatter\TokenQueue;
use PhpFormatter\Formatter;

interface ITransformation
{

	public function canApply(Token $token, TokenQueue $queue);

	public function transform(Token $token, TokenQueue $inputQueue, TokenQueue $outputQueue, Formatter $formatter);

}