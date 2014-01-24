<?php

namespace PhpFormatter;

class Formatter
{

	protected $settings;

	protected $transformations;

	public function __construct($settings = [])
	{
		$this->settings = $settings;

		$this->transformations = [];

		foreach ($settings as $key => $value) {
			if ($key === 'constants') {
				$this->transformations[] = new Transformation\Constants($value);
			} elseif ($key === 'strings/join') {
				$this->transformations[] = new Transformation\Strings\Join($value);
			} elseif ($key === 'brackets') {
				$this->transformations[] = new Transformation\Brackets($value);
			}
		}
	}

	public function format($code)
	{
		$tokenQueue = new TokenQueue(token_get_all($code));

		return $this->render($this->processTokenQueue($tokenQueue));
	}

	public function processTokenQueue(TokenQueue $tokenQueue)
	{
		$processedTokenQueue = new TokenQueue;

		while (!$tokenQueue->isEmpty()) {
			$token = $tokenQueue->dequeue();

			$transformed = FALSE;
			foreach ($this->transformations as $transformation) {
				if ($transformation->canApply($token, $tokenQueue)) {
					$transformation->transform($token, $tokenQueue, $processedTokenQueue, $this);
					$transformed = TRUE;
					break;
				}
			}

			if (!$transformed) {
				$processedTokenQueue[] = $token;
			}
		}

		return $processedTokenQueue;
	}

	protected function render(TokenQueue $tokenQueue)
	{
		$string = '';
		foreach ($tokenQueue as $token)
		{
			$string .= $token;
		}
		return $string;
	}

}
