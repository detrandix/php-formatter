<?php

namespace PhpFormatter\Transformation;

use PhpFormatter\Indent;
use PhpFormatter\ControlStructures;
use PhpFormatter\Token;
use PhpFormatter\TokenList;
use PhpFormatter\Formatter;

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

	public function registerToFormatter(Formatter $formatter, $settings)
	{
		if (isset($settings['braces'])) {
			$bracesSettings = $settings['braces'];

			if (isset($bracesSettings['class-declaration']) && in_array($bracesSettings['class-declaration'], self::$POSSIBLE_OPTIONS)) {
				$formatter->addTransformation(new Token('{'), [$this, 'processLeftBefore'], Formatter::USE_BEFORE, [T_CLASS, $bracesSettings['class-declaration']]);
				$formatter->addTransformation(new Token('{'), [$this, 'processLeftAfter'], Formatter::USE_AFTER, [T_CLASS, $bracesSettings['class-declaration']]);
				$formatter->addTransformation(new Token('}'), [$this, 'processRightBefore'], Formatter::USE_BEFORE, [T_CLASS, $bracesSettings['class-declaration']]);
			}

			if (isset($bracesSettings['if-elseif-else']) && in_array($bracesSettings['if-elseif-else'], self::$POSSIBLE_OPTIONS)) {
				$formatter->addTransformation(new Token('{'), [$this, 'processLeftBefore'], Formatter::USE_BEFORE, [T_IF, $bracesSettings['if-elseif-else']]);
				$formatter->addTransformation(new Token('{'), [$this, 'processLeftAfter'], Formatter::USE_AFTER, [T_IF, $bracesSettings['if-elseif-else']]);
				$formatter->addTransformation(new Token('}'), [$this, 'processRightBefore'], Formatter::USE_BEFORE, [T_IF, $bracesSettings['if-elseif-else']]);

				$formatter->addTransformation(new Token('{'), [$this, 'processLeftBefore'], Formatter::USE_BEFORE, [T_ELSEIF, $bracesSettings['if-elseif-else']]);
				$formatter->addTransformation(new Token('{'), [$this, 'processLeftAfter'], Formatter::USE_AFTER, [T_ELSEIF, $bracesSettings['if-elseif-else']]);
				$formatter->addTransformation(new Token('}'), [$this, 'processRightBefore'], Formatter::USE_BEFORE, [T_ELSEIF, $bracesSettings['if-elseif-else']]);

				$formatter->addTransformation(new Token('{'), [$this, 'processLeftBefore'], Formatter::USE_BEFORE, [T_ELSE, $bracesSettings['if-elseif-else']]);
				$formatter->addTransformation(new Token('{'), [$this, 'processLeftAfter'], Formatter::USE_AFTER, [T_ELSE, $bracesSettings['if-elseif-else']]);
				$formatter->addTransformation(new Token('}'), [$this, 'processRightBefore'], Formatter::USE_BEFORE, [T_ELSE, $bracesSettings['if-elseif-else']]);
			}

			if (isset($bracesSettings['for-foreach']) && in_array($bracesSettings['for-foreach'], self::$POSSIBLE_OPTIONS)) {
				$formatter->addTransformation(new Token('{'), [$this, 'processLeftBefore'], Formatter::USE_BEFORE, [T_FOR, $bracesSettings['for-foreach']]);
				$formatter->addTransformation(new Token('{'), [$this, 'processLeftAfter'], Formatter::USE_AFTER, [T_FOR, $bracesSettings['for-foreach']]);
				$formatter->addTransformation(new Token('}'), [$this, 'processRightBefore'], Formatter::USE_BEFORE, [T_FOR, $bracesSettings['for-foreach']]);
				$formatter->addTransformation(new Token('}'), [$this, 'processRightAfter'], Formatter::USE_AFTER, [T_FOR, $bracesSettings['for-foreach']]);

				$formatter->addTransformation(new Token('{'), [$this, 'processLeftBefore'], Formatter::USE_BEFORE, [T_FOREACH, $bracesSettings['for-foreach']]);
				$formatter->addTransformation(new Token('{'), [$this, 'processLeftAfter'], Formatter::USE_AFTER, [T_FOREACH, $bracesSettings['for-foreach']]);
				$formatter->addTransformation(new Token('}'), [$this, 'processRightBefore'], Formatter::USE_BEFORE, [T_FOREACH, $bracesSettings['for-foreach']]);
				$formatter->addTransformation(new Token('}'), [$this, 'processRightAfter'], Formatter::USE_AFTER, [T_FOREACH, $bracesSettings['for-foreach']]);
			}
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
		if ($this->controlStructures->isActualType($params[0])) {
			$processedTokenList[] = new Token("\n", T_WHITESPACE);
			$this->indent->addIndent($processedTokenList);
		}
	}

	public function processRightBefore($token, $tokenList, $processedTokenList, $params)
	{
		if ($this->controlStructures->isActualType($params[0])) {
			while ($processedTokenList->tail()->isType(T_WHITESPACE)) {
				$processedTokenList->pop();
			}

			$processedTokenList[] = new Token("\n", T_WHITESPACE);

			$this->indent->addIndent($processedTokenList, $params[1] === 'new-line-idented' ? 1 : 0);
		}
	}

	public function processRightAfter($token, $tokenList, $processedTokenList, $params)
	{
		if ($this->controlStructures->isLastType($params[0])) {
			$processedTokenList[] = new Token("\n", T_WHITESPACE);

			$this->indent->addIndent($processedTokenList);
		}
	}

}
