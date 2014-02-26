<?php

namespace PhpFormatter\Transformation;

use PhpFormatter\Indent;
use PhpFormatter\ControlStructures;
use PhpFormatter\Token;
use PhpFormatter\TokenList;
use PhpFormatter\TransformationRules;

class Braces
{

	protected $controlStructures;

	protected $indent;

	static protected $POSSIBLE_OPTIONS = ['new-line', 'new-line-idented', 'same-line'];

	public function __construct(ControlStructures $controlStructures, Indent $indent)
	{
		$this->controlStructures = $controlStructures;
		$this->indent = $indent;
	}

	public function register(TransformationRules $rules, $settings)
	{
		$rules->addRuleBySingleValue('{', TransformationRules::USE_AFTER, [$this, 'processLeftAfter']);
		$rules->addRuleBySingleValue('}', TransformationRules::USE_BEFORE, [$this, 'processRightBefore'], NULL);
		$rules->addRuleBySingleValue('}', TransformationRules::USE_AFTER, [$this, 'processRightAfter']);

		if (isset($settings['braces'])) {
			$bracesSettings = $settings['braces'];

			if (isset($bracesSettings['class-declaration']) && in_array($setting = $bracesSettings['class-declaration'], self::$POSSIBLE_OPTIONS)) {
				$this->registerRules($rules, T_CLASS, $setting);
			}

			if (isset($bracesSettings['if-elseif-else']) && in_array($setting = $bracesSettings['if-elseif-else'], self::$POSSIBLE_OPTIONS)) {
				$this->registerRules($rules, T_IF, $setting);
				$this->registerRules($rules, T_ELSEIF, $setting, TRUE);
				$this->registerRules($rules, T_ELSE, $setting, TRUE);
			}

			if (isset($bracesSettings['for-foreach']) && in_array($setting = $bracesSettings['for-foreach'], self::$POSSIBLE_OPTIONS)) {
				$this->registerRules($rules, T_FOR, $setting);
				$this->registerRules($rules, T_FOREACH, $setting);
			}

			if (isset($bracesSettings['while-do']) && in_array($setting = $bracesSettings['while-do'], self::$POSSIBLE_OPTIONS)) {
				$this->registerRules($rules, T_WHILE, $setting, TRUE);
				$this->registerRules($rules, T_DO, $setting);
			}

			if (isset($bracesSettings['switch']) && in_array($setting = $bracesSettings['switch'], self::$POSSIBLE_OPTIONS)) {
				$this->registerRules($rules, T_SWITCH, $setting);
			}

			if (isset($bracesSettings['try-catch']) && in_array($setting = $bracesSettings['try-catch'], self::$POSSIBLE_OPTIONS)) {
				$this->registerRules($rules, T_TRY, $setting);
				$this->registerRules($rules, T_CATCH, $setting, TRUE);
			}
		}
	}

	protected function registerRules(TransformationRules $rules, $type, $setting, $afterEndBrace = FALSE)
	{
		$rules->addRuleBySingleValue('{', TransformationRules::USE_BEFORE, [$this, 'processLeftBefore'], [$type, $setting]);
		$rules->addRuleBySingleValue('{', TransformationRules::USE_AFTER, [$this, 'processLeftAfter'], [$type, $setting]);
		$rules->addRuleBySingleValue('}', TransformationRules::USE_BEFORE, [$this, 'processRightBefore'], [$type, $setting]);

		if ($afterEndBrace) {
			$rules->addRuleBySingleValue('}', TransformationRules::USE_AFTER, [$this, 'processRightAfter'], [$type, $setting]);
		}
	}

	public function processLeftBefore($token, $tokenList, $processedTokenList, $params)
	{
		if ($this->controlStructures->isActualType($params[0])) {
			switch ($params[1]) {
				case 'new-line':
					$processedTokenList[] = new Token("\n", T_WHITESPACE);
					$this->indent->addIndent($processedTokenList);
					break;
				case 'new-line-idented':
					$processedTokenList[] = new Token("\n", T_WHITESPACE);
					$this->indent->addIndent($processedTokenList, 1);
					break;
				case 'same-line':
					break;
			}
		}
	}

	public function processLeftAfter($token, $tokenList, $processedTokenList, $params)
	{
		while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
			$processedTokenList->pop();
		}

		$processedTokenList[] = new Token("\n", T_WHITESPACE);
		$this->indent->addIndent($processedTokenList);
	}

	public function processRightBefore($token, $tokenList, $processedTokenList, $params)
	{
		if ($params === NULL) {
			while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
				$processedTokenList->pop();
			}

			$processedTokenList[] = new Token("\n", T_WHITESPACE);
			$this->indent->addIndent($processedTokenList);
		} elseif ($this->controlStructures->isActualType($params[0])) {
			while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
				$processedTokenList->pop();
			}

			$processedTokenList[] = new Token("\n", T_WHITESPACE);

			$this->indent->addIndent($processedTokenList, $params[1] === 'new-line-idented' ? 1 : 0);
		}
	}

	/**
	 * @todo tohle dat obecne, a pak si jednotlive prikazy T_ELSEIF pripadne ten enter pres sebou smazou
	 */
	public function processRightAfter($token, $tokenList, $processedTokenList, $params)
	{
		while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
			$processedTokenList->pop();
		}

		$skipNewline =
			($params[0] === T_WHILE && $this->controlStructures->isLastType(T_DO))
			|| ($params[0] === T_CATCH)
			|| ($params[0] === T_ELSEIF)
			|| ($params[0] === T_ELSE);

		if (!$skipNewline) {
			$processedTokenList[] = new Token("\n", T_WHITESPACE);
			$this->indent->addIndent($processedTokenList);
		}
	}

}
