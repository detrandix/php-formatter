<?php

use PhpFormatter\Token;

require_once __DIR__ . '/../bootstrap.php';


$token = new Token('string', T_STRING);

Assert::true($token->isType(T_STRING));
Assert::true($token->isType('T_STRING'));

Assert::true($token->isInTypes([T_STRING, T_WHILE]));
Assert::false($token->isInTypes([T_WHILE]));
Assert::false($token->isInTypes([]));

Assert::same($token->getType(), 'T_STRING');

Assert::false($token->isSingleValue('string'));



$token = new Token('string');

Assert::true($token->isType(NULL));

Assert::same($token->getType(), NULL);
Assert::false($token->isInTypes([]));

Assert::true($token->isSingleValue('string'));

Assert::true($token->isInSingleValues(['string', 'test']));



$token = Token::createFromZendToken(array(T_STRING, 'string', 1));

Assert::true($token->isType(T_STRING));

Assert::same($token->getValue(), 'string');



$token = Token::createFromZendToken('.');

Assert::true($token->isSingleValue('.'));



$token = new Token('string', T_STRING);
$token2 = new Token('string');

Assert::true($token->isSame($token));
Assert::false($token->isSame($token2));
