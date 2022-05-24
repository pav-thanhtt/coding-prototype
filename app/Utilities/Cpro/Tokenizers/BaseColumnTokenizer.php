<?php

namespace App\Utilities\Cpro\Tokenizers;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Tokenizers\Interfaces\ColumnTokenizerInterface;

abstract class BaseColumnTokenizer implements ColumnTokenizerInterface
{
    protected array $tokens = [];
    protected ColumnDefinition $definition;

    public function __construct(array $value)
    {
        $this->definition = new ColumnDefinition();
        $this->tokens = $value;
    }

    public function definition(): ColumnDefinition
    {
        return $this->definition;
    }

    /**
     * @param array $row
     * @return static
     */
    public static function parse(array $row): static
    {
        return (new static($row))->tokenize();
    }

    protected function consume(string $key)
    {
        return $this->tokens[$key];
    }
}
