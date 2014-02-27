<?php

/**
 * This file is part of the PhpFormatter tool
 *
 * Copyright (c) 2014 Tomáš Lang
 *
 * For the full copyright and license information, please view the file
 * license-mit.txt that was distributed with this source code.
 */

namespace PhpFormatter;

class Formatter
{

	/** @var TransformationRules */
	protected $transformationRules;

	private function __construct()
	{
		$this->transformationRules = new TransformationRules;
	}

	/**
	 * @param  array $settings
	 *
	 * @return self
	 */
	public static function createFromSettings($settings = [])
	{
		$formatter = new self;

		$controlStructures = new ControlStructures;
		$controlStructures->register($formatter->getTransformationRules());

		$indent = new Indent($settings);
		$indent->register($formatter->getTransformationRules());

		$spaces = new Transformation\Spaces($controlStructures, $indent);
		$spaces->register($formatter->getTransformationRules(), $settings);

		$newLine = new Transformation\NewLine($controlStructures, $indent);
		$newLine->register($formatter->getTransformationRules(), $settings);

		$braces = new Transformation\Braces($controlStructures, $indent);
		$braces->register($formatter->getTransformationRules(), $settings);

		return $formatter;
	}

	/**
	 * @return TransformationRules
	 */
	public function getTransformationRules()
	{
		return $this->transformationRules;
	}

	/**
	 * @param  string $code
	 *
	 * @return string
	 */
	public function format($code)
	{
		$tokenList = new TokenList(token_get_all($code));

		return $this->render($this->processTokenList($tokenList));
	}

	/**
	 * @param  TokenList $tokenList
	 *
	 * @return TokenList
	 */
	public function processTokenList(TokenList $tokenList)
	{
		$processedTokenList = new TokenList;

		while (!$tokenList->isEmpty()) {
			$token = $tokenList->shift();

			if (!$token->isType(T_WHITESPACE)) {
				$transformations = $this->transformationRules->getTransformations($token);

				$this->processToken($token, $tokenList, $processedTokenList, $transformations);
			} else {
				$whitespace = $token->getValue();

				while (!$tokenList->isEmpty() && $tokenList->head()->isType(T_WHITESPACE)) {
					$whitespace .= $tokenList->shift()->getValue();
				}

				$countNewLines = max(0, substr_count($token->getValue(), "\n") - 1);

				if ($countNewLines > 0) {
					$spacesBefore = [];
					while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
						$spacesBefore[] = $processedTokenList->pop();
					}

					$processedTokenList[] = new Token(str_repeat("\n", $countNewLines), T_WHITESPACE);

					foreach (array_reverse($spacesBefore) as $space) {
						$processedTokenList[] = $space;
					}
				}
			}
		}

		while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
			$processedTokenList->pop();
		}

		return $processedTokenList;
	}

	/**
	 * @param Token     $token
	 * @param TokenList $tokenList
	 * @param TokenList $processedTokenList
	 * @param array     $transformations
	 */
	protected function processToken(Token $token, TokenList $tokenList, TokenList $processedTokenList, array $transformations)
	{
		foreach ($transformations[transformationRules::USE_BEFORE] as $transformation) {
			$transformation[0]($token, $tokenList, $processedTokenList, $transformation[1]);
		}

		$processedTokenList[] = $token;

		foreach ($transformations[transformationRules::USE_AFTER] as $transformation) {
			$transformation[0]($token, $tokenList, $processedTokenList, $transformation[1]);
		}
	}

	/**
	 * @param  TokenList $tokenList
	 *
	 * @return string
	 */
	public function render(TokenList $tokenList)
	{
		$string = '';
		foreach ($tokenList as $token)
		{
			$string .= $token;
		}
		return $string;
	}

}
