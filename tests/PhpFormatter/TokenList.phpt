<?php

use PhpFormatter\TokenList;
use PhpFormatter\Token;

require_once __DIR__ . '/../bootstrap.php';


$tokenList = new TokenList(['.', new Token('test', T_STRING)]);

Assert::false($tokenList->isEmpty());

Assert::same($tokenList->count(), 2);

Assert::true($tokenList->tail(1)->isSingleValue('.'));

$head = $tokenList->head();
Assert::true($head instanceof Token);
Assert::same($head, $tokenList->shift());
Assert::true($head->isSingleValue('.'));

$tail = $tokenList->tail();
Assert::true($tail instanceof Token);
Assert::same($tail, $tokenList->pop());
Assert::true($tail->isType(T_STRING));

Assert::true($tokenList->isEmpty());

Assert::null($tokenList->tail(1));
