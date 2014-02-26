<?php

use PhpFormatter\ControlStructures;
use PhpFormatter\Token;

require_once __DIR__ . '/../bootstrap.php';


$controlStructures = new ControlStructures;

Assert::false($controlStructures->isActualType(T_IF));
Assert::false($controlStructures->isLastType(T_IF));

$controlStructures->addControl(new Token('if', T_IF));

Assert::true($controlStructures->isActualType(T_IF));

$controlStructures->addLeftBrace();

Assert::true($controlStructures->isActualType(T_IF));

$controlStructures->addRightBrace();

Assert::false($controlStructures->isActualType(T_IF));
Assert::true($controlStructures->isLastType(T_IF));



$controlStructures->addControl(new Token('if', T_IF));
$controlStructures->addLeftBrace();
$controlStructures->addLeftBrace();

Assert::false($controlStructures->isActualType(T_IF));

$controlStructures->addRightBrace();

Assert::true($controlStructures->isActualType(T_IF));



$controlStructures = new ControlStructures;

$controlStructures->addControl(new Token('if', T_IF));
$controlStructures->addSemicolon();

Assert::true($controlStructures->isLastType(T_IF));

$controlStructures->addControl(new Token('if', T_IF));
$controlStructures->addLeftBrace();
$controlStructures->addSemicolon();

Assert::true($controlStructures->isActualType(T_IF));

$controlStructures->addControl(new Token('for', T_FOR));
$controlStructures->addSemicolon();

Assert::true($controlStructures->isActualType(T_FOR));
