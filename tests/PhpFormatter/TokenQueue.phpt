<?php

use PhpFormatter\TokenQueue;
use PhpFormatter\Token;

require_once __DIR__ . '/../bootstrap.php';


$token = new Token('test', T_STRING);
$tokenQueue = new TokenQueue(['.', $token]);

Assert::same($tokenQueue->count(), 2);

Assert::true($tokenQueue->bottom() instanceof Token);

Assert::true(($token = $tokenQueue->dequeue()) instanceof Token);
Assert::true($token->isSingleValue('.'));

Assert::true(($token = $tokenQueue->dequeue()) instanceof Token);
Assert::true($token->isType(T_STRING));
Assert::same($token->getValue(), 'test');
