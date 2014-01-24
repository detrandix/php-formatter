<?php

namespace PhpFormatter\Change;

use PhpFormatter\Token;
use PhpFormatter\TokenQueue;

interface IChange
{

	public function canApply(Token $token, TokenQueue $queue);

	public function apply(Token $token, TokenQueue $inputQueue, TokenQueue $outputQueue);

}