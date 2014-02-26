<?php

namespace PhpFormatter;

class Formatter
{
	protected $transformationRules;

	private function __construct()
	{
		$this->transformationRules = new TransformationRules;
	}

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

	public function getTransformationRules()
	{
		return $this->transformationRules;
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
				$transformations = $this->transformationRules->getTransformations($token);

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
		foreach ($transformations[transformationRules::USE_BEFORE] as $transformation) {
			$transformation[0]($token, $tokenList, $processedTokenList, $transformation[1]);
		}

		$processedTokenList[] = $token;

		foreach ($transformations[transformationRules::USE_AFTER] as $transformation) {
			$transformation[0]($token, $tokenList, $processedTokenList, $transformation[1]);
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
