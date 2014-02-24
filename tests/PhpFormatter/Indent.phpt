<?php

use PhpFormatter\Indent;
use PhpFormatter\TokenList;

require_once __DIR__ . '/../bootstrap.php';


$indent = new Indent;
$tokenList = new TokenList;

$indent->addIndent($tokenList);

Assert::same(0, $tokenList->count());

$indent->incIndent();

Assert::same(0, $tokenList->count());



$indent = new Indent([
	'indent' => [
		'type' => 'tab',
		'count' => 10
	]
]);
$tokenList = new TokenList;

$indent->addIndent($tokenList);

Assert::same(0, $tokenList->count());

$indent->incIndent();
$indent->addIndent($tokenList);

Assert::same("\t", $tokenList->tail()->getValue());



Assert::exception(function() {
	$indent = new Indent([
		'indent' => [
			'type' => 'space'
		]
	]);
}, 'InvalidArgumentException', '@todo');



$indent = new Indent([
	'indent' => [
		'type' => 'space',
		'count' => 4
	]
]);
$tokenList = new TokenList;

$indent->addIndent($tokenList);

Assert::same(0, $tokenList->count());

$indent->incIndent();
$indent->addIndent($tokenList);

Assert::same("    ", $tokenList->tail()->getValue());
