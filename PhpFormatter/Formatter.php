<?php

namespace PhpFormatter;

class Formatter
{

	protected $settings;

	protected $transformations;

	protected $lastTransformation = NULL;

	protected static $transformationClassMap = [
		'constants' => 'PhpFormatter\\Transformation\\Constants',
		'strings/join' => 'PhpFormatter\\Transformation\\Strings\\Join',
		'strings/semicolon' => 'PhpFormatter\\Transformation\\Strings\\Semicolon',
		'brackets' => 'PhpFormatter\\Transformation\\Brackets',
		'curly-brackets' => 'PhpFormatter\\Transformation\\CurlyBrackets'
	];

	public function __construct($settings = [])
	{
		$this->settings = $settings;

		$this->transformations = [];

		foreach ($settings as $key => $value) {
			if (isset(self::$transformationClassMap[$key])) {
				$this->transformations[] = new self::$transformationClassMap[$key]($value);
			}
		}
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

			$transformed = FALSE;
			foreach ($this->transformations as $transformation) {
				if ($transformation->canApply($token, $tokenList)) {
					$transformation->transform($token, $tokenList, $processedTokenList, $this);
					$this->lastTransformation = $transformation;
					$transformed = TRUE;
					break;
				}
			}

			if (!$transformed) {
				$processedTokenList[] = $token;
				$this->lastTransformation = NULL;
			}
		}

		return $processedTokenList;
	}

	public function getLastTransformation()
	{
		return $this->lastTransformation;
	}

	protected function render(TokenList $tokenList)
	{
		$string = '';
		foreach ($tokenList as $token)
		{
			$string .= $token;
		}
		return $string;
	}

}
