<?php

use PhpFormatter\TransformationRules;
use PhpFormatter\Token;

require_once __DIR__ . '/../bootstrap.php';


$transformationRules = new TransformationRules;

$transformationRules->addRuleByType(T_IF, TransformationRules::USE_BEFORE, 'if before');
$transformationRules->addRuleByType(T_IF, TransformationRules::USE_AFTER, 'if after');
$transformationRules->addRuleByType([T_IF, T_WHILE], TransformationRules::USE_BEFORE | TransformationRules::USE_AFTER, 'if before after');

$transformationRules->addRuleBySingleValue('if', TransformationRules::USE_BEFORE, 'if before value');
$transformationRules->addRuleBySingleValue('if', TransformationRules::USE_AFTER, 'if after value');
$transformationRules->addRuleBySingleValue(['if', 'while'], TransformationRules::USE_BEFORE | TransformationRules::USE_AFTER, 'if before after value');

Assert::same([
	TransformationRules::USE_BEFORE => ['if before', 'if before after'],
	TransformationRules::USE_AFTER => ['if after', 'if before after']
], $transformationRules->getTransformations(new Token('if', T_IF)));

Assert::same([
	TransformationRules::USE_BEFORE => ['if before value', 'if before after value'],
	TransformationRules::USE_AFTER => ['if after value', 'if before after value']
], $transformationRules->getTransformations(new Token('if', NULL)));

Assert::same([
	TransformationRules::USE_BEFORE => [],
	TransformationRules::USE_AFTER => []
], $transformationRules->getTransformations(new Token('if', T_DO)));
