<?php

namespace PhpFormatter;

class Formatter
{

	const USE_BEFORE = 'before';
	const USE_AFTER = 'after';

	protected $transformations;

	private function __construct()
	{
		$this->transformations = [];
	}

	public static function createFromSettings($settings = [])
	{
		$formatter = new self;

		$controlStructures = new ControlStructures;
		$controlStructures->registerToFormatter($formatter);

		$indent = new Indent($settings);
		$indent->registerToFormatter($formatter);

		$spaces = new Transformation\Spaces($controlStructures, $indent);
		$spaces->registerToFormatter($formatter, $settings);

		$newLine = new Transformation\NewLine($controlStructures, $indent);
		$newLine->registerToFormatter($formatter, $settings);

		$braces = new Transformation\Braces($controlStructures, $indent);
		$braces->registerToFormatter($formatter, $settings);

		return $formatter;
	}

	public function addTransformation(Token $token, $callback, $use, $params = NULL)
	{
		$this->transformations[] = [$token, $callback, $use, $params];
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

		while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
			$processedTokenList->pop();
		}

		return $processedTokenList;
	}

	protected function processToken($token, $tokenList, $processedTokenList, $transformations)
	{
		foreach ($transformations as $transformation) {
			if ($transformation[2] === self::USE_BEFORE) {
				$transformation[1]($token, $tokenList, $processedTokenList, $transformation[3]);
			}
		}

		$processedTokenList[] = $token;

		foreach ($transformations as $transformation) {
			if ($transformation[2] === self::USE_AFTER) {
				$transformation[1]($token, $tokenList, $processedTokenList, $transformation[3]);
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
