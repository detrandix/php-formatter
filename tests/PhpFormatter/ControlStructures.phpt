<?php

use PhpFormatter\ControlStructures;
use PhpFormatter\Token;

require_once __DIR__ . '/../bootstrap.php';


$controlStructures = new ControlStructures;

Assert::false($controlStructures->isActualType(T_IF));

$controlStructures->addControl(new Token('if', T_IF));

Assert::true($controlStructures->isActualType(T_IF));

$controlStructures->addLeftBrace();

Assert::true($controlStructures->isActualType(T_IF));

$controlStructures->addRightBrace();

Assert::false($controlStructures->isActualType(T_IF));



$controlStructures->addControl(new Token('if', T_IF));
$controlStructures->addLeftBrace();
$controlStructures->addLeftBrace();

Assert::false($controlStructures->isActualType(T_IF));

$controlStructures->addRightBrace();

Assert::true($controlStructures->isActualType(T_IF));
