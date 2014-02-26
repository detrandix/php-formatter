<?php

namespace PhpFormatter;

class Indent
{

	const TYPE_NONE = '';
	const TYPE_TAB = "\t";
	const TYPE_SPACE = ' ';

	protected $type;

	protected $count;

	protected $indent;

	public function __construct($settings = [])
	{
		$type = self::TYPE_NONE;
		$count = 0;

		if (isset($settings['indent']))
		{
			$indentSettings = $settings['indent'];

			if (isset($indentSettings['type'])) {
				switch ($indentSettings['type']) {
					case 'tab':
						$type = self::TYPE_TAB;
						$count = 1;
						break;
					case 'space':
						if (!isset($indentSettings['count'])) {
							throw new \InvalidArgumentException('@todo');
						}

						$type = self::TYPE_SPACE;
						$count = (int) $indentSettings['count'];
						break;
				}
			}
		}

		$this->type = $type;
		$this->count = $count;
		$this->indent = 0;
	}

	public function register(TransformationRules $rules)
	{
		$rules->addRuleBySingleValue('{', TransformationRules::USE_AFTER, [$this, 'incIndent']);
		$rules->addRuleBySingleValue('}', TransformationRules::USE_BEFORE, [$this, 'decIndent']);
	}

	public function addIndent(TokenList $tokenList, $indentAdd = 0)
	{
		$indent = $this->indent + $indentAdd;
		if ($indent > 0 && $this->count > 0 && $this->type !== self::TYPE_NONE) {
			$tokenList->push(new Token(str_repeat(str_repeat($this->type, $this->count), $indent), T_WHITESPACE));
		}
	}

	public function incIndent()
	{
		$this->indent++;
	}

	public function decIndent()
	{
		$this->indent = max(0, $this->indent - 1);
	}

}
