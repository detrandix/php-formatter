<?php

use PhpFormatter\Token;

require_once __DIR__ . '/../bootstrap.php';


$token = new Token('string', T_STRING);

Assert::true($token->isType(T_STRING));

Assert::true($token->isType('T_STRING'));

Assert::false($token->isSingleValue('string'));



$token = new Token('string');

Assert::true($token->isType(NULL));

Assert::true($token->isSingleValue('string'));



$token = Token::createFromZendToken(array(T_STRING, 'string', 1));

Assert::true($token->isType(T_STRING));

Assert::same($token->getValue(), 'string');



$token = Token::createFromZendToken('.');

Assert::true($token->isSingleValue('.'));
