<?php

namespace App\Utilities\Cpro\Generators\Interfaces;

use App\Utilities\Cpro\Definitions\TableDefinition;

interface TableGeneratorInterface
{
    public function resolveStructure();

    public function parse();

    public function definition(): TableDefinition;
}
