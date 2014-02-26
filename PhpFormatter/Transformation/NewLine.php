<?php

namespace PhpFormatter\Transformation;

use PhpFormatter\Indent;
use PhpFormatter\ControlStructures;
use PhpFormatter\Token;
use PhpFormatter\TokenList;
use PhpFormatter\Formatter;

class NewLine
{

	protected $controlStructures;

	protected $indent;

	public function __construct(ControlStructures $controlStructures, Indent $indent)
	{
		$this->controlStructures = $controlStructures;
		$this->indent = $indent;
	}

	public function registerToFormatter(Formatter $formatter, $settings)
	{
		$formatter->addTransformation(new Token(';'), [$this, 'addNewLineAfterSemicolon'], Formatter::USE_AFTER);

		if (isset($settings['new-line'])) {
			$newLineSettings = $settings['new-line'];

			if (isset($newLineSettings['else-elseif']) && $newLineSettings['else-elseif']) {
				$formatter->addTransformation(new Token('else', T_ELSE), [$this, 'addNewLineBefore'], Formatter::USE_BEFORE);
				$formatter->addTransformation(new Token('elseif', T_ELSEIF), [$this, 'addNewLineBefore'], Formatter::USE_BEFORE);
			}

			if (isset($newLineSettings['while']) && $newLineSettings['while']) {
				$formatter->addTransformation(new Token('while', T_WHILE), [$this, 'addNewLineBeforeWhile'], Formatter::USE_BEFORE);
			}

			if (isset($newLineSettings['catch']) && $newLineSettings['catch']) {
				$formatter->addTransformation(new Token('catch', T_CATCH), [$this, 'addNewLineBefore'], Formatter::USE_BEFORE);
			}
		}
	}

	public function addNewLineAfterSemicolon($token, $tokenList, $processedTokenList, $params)
	{
		if (!$this->controlStructures->isActualType(T_FOR)) {
			while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
				$processedTokenList->pop();
			}

			$processedTokenList[] = new Token("\n", T_WHITESPACE);
			$this->indent->addIndent($processedTokenList);
		}
	}

	public function addNewLineBefore($token, $tokenList, $processedTokenList, $params)
	{
		while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
			$processedTokenList->pop();
		}

		$processedTokenList[] = new Token("\n", T_WHITESPACE);
	}

	public function addNewLineBeforeWhile($token, $tokenList, $processedTokenList, $params)
	{
		if ($this->controlStructures->isLastType(T_DO)) {
			while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
				$processedTokenList->pop();
			}

			$processedTokenList[] = new Token("\n", T_WHITESPACE);
		}
	}

}
