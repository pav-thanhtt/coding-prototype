<?php

namespace App\Utilities\Cpro\Tokenizers\Interfaces;

use App\Utilities\Cpro\Definitions\ColumnDefinition;

interface ColumnTokenizerInterface
{
    public function tokenize(): self;

    public function definition(): ColumnDefinition;
}
