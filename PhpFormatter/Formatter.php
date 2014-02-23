<?php

namespace PhpFormatter;

class Formatter
{

	const USE_BEFORE = 'before';
	const USE_AFTER = 'after';

	protected $settings;

	protected $transformations;

	public function __construct($settings = [])
	{
		$this->settings = $settings;
		$this->transformations = [];
	}

	public function addTransformation(Token $token, $callback, $use)
	{
		$this->transformations[] = [$token, $callback, $use];
	}

	public function format($code)
	{
		$tokenList = new TokenList(token_get_all($code));

		return $this->render($this->processTokenList($tokenList));
	}

	public function processTokenList(TokenList $tokenList)
	{
		$processedTokenList = new TokenList;

		while (!$tokenList->isEmpty()) {
			$token = $tokenList->shift();

			if (!$token->isType(T_WHITESPACE)) {
				$transformations = [];

				foreach ($this->transformations as $transformation) {
					if ($token->isSame($transformation[0])) {
						$transformations[] = $transformation;
					}
				}

				$this->processToken($token, $tokenList, $processedTokenList, $transformations);
			}
		}

		return $processedTokenList;
	}

	protected function processToken($token, $tokenList, $processedTokenList, $transformations)
	{
		foreach ($transformations as $transformation) {
			if ($transformation[2] === self::USE_BEFORE) {
				$transformation[1]($token, $tokenList, $processedTokenList);
			}
		}

		$processedTokenList[] = $token;

		foreach ($transformations as $transformation) {
			if ($transformation[2] === self::USE_AFTER) {
				$transformation[1]($token, $tokenList, $processedTokenList);
			}
		}
	}

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
