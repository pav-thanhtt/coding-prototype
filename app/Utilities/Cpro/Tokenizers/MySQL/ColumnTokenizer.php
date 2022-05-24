<?php

namespace App\Utilities\Cpro\Tokenizers\MySQL;

use Illuminate\Support\Str;
use App\Utilities\Cpro\Tokenizers\BaseColumnTokenizer;

class ColumnTokenizer extends BaseColumnTokenizer
{

    public function tokenize(): self
    {
        $this->consumeColumnName();
        $this->consumeColumnType();
        $this->consumeUnsigned();

        $this->consumeNullable();
        $this->consumeCollation();
        $this->consumeAutoIncrement();
        $this->consumeComment();
        $this->consumeKeyConstraints();
        $this->consumeDefaultValue();
        return $this;
    }

    protected function consumeColumnName()
    {
        $this->definition->setColumnName($this->consume('Field'));
    }

    protected function consumeColumnType()
    {
        $piece = $this->consume('Type');
        preg_match('/^\w+/', $piece, $matches);
        $this->definition->setColumnDataType($matches[0]);

        if (preg_match("/\((.+?)\)/", $piece, $constraintMatches)) {
            $matches = explode(',', $constraintMatches[1]);
            $this->resolveColumnConstraints($matches);
        }
    }

    private function consumeAutoIncrement()
    {
        $this->definition->setAutoIncrementing($this->consume('Extra') === 'auto_increment');
    }

    protected function consumeNullable()
    {
        $this->definition->setNullable($this->consume('Null') === 'YES');
    }

    protected function consumeDefaultValue()
    {
        $this->definition->setDefaultValue($this->consume('Default'));
    }

    protected function consumeComment()
    {
        $this->definition->setComment($this->consume('Comment'));
    }

    protected function consumeCollation()
    {
        $this->definition->setCollation($this->consume('Collation'));
    }

    private function consumeUnsigned()
    {
        $isUnsigned = Str::contains($this->consume('Type'), 'unsigned');
        $this->definition->setUnsigned($isUnsigned);
    }

    private function consumeKeyConstraints()
    {
        switch ($this->consume('Key')) {
            case 'PRI':
                $this->definition->setPrimary(true);
                break;
            case 'UNI':
                $this->definition->setUnique(true);
        }
    }

    private function resolveColumnConstraints(array $constraints)
    {
        if ($this->getColumnDataType() === 'char' && count($constraints) === 1 && $constraints[0] == 36) {
            //uuid for mysql
            $this->definition->setIsUUID(true);
            return;
        }
        if ($this->isArrayType()) {
            $this->definition->setMethodParameters(array_map(fn($item) => trim($item, '\''), $constraints));
        } else {
            if (Str::contains(strtoupper($this->getColumnDataType()), 'INT')) {
                if ($this->getColumnDataType() === 'tinyint' && count($constraints) === 1 && $constraints[0] === '1') {
                    $this->definition->setMethodParameters([1]); //this is boolean
                } else {
                    $this->definition->setMethodParameters([]); //laravel does not like display field widths
                }
            } else {
                $this->definition->setMethodParameters(array_map(fn($item) => (int)$item, $constraints));
            }
        }
    }

    //endregion

    protected function isTextType(): bool
    {
        return Str::contains($this->getColumnDataType(), ['char', 'text', 'set', 'enum']);
    }

    protected function isNumberType(): bool
    {
        return Str::contains($this->getColumnDataType(), ['int', 'decimal', 'float', 'double']);
    }

    protected function isArrayType(): bool
    {
        return Str::contains($this->getColumnDataType(), ['enum', 'set']);
    }

    /**
     * @return string
     */
    public function getColumnDataType(): string
    {
        return $this->definition->getColumnDataType();
    }
}
