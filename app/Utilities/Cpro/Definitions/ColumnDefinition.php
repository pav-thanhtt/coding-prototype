<?php

namespace App\Utilities\Cpro\Definitions;

/**
 * Class ColumnDefinition
 * @package LaravelMigrationGenerator\Definitions
 */
class ColumnDefinition
{

    protected string $columnDataType;

    protected array $methodParameters = [];

    protected ?string $columnName;

    protected bool $unsigned = false;

    protected ?bool $nullable = null;

    protected $defaultValue;

    protected ?string $comment = null;

    protected ?string $collation = null;

    protected bool $autoIncrementing = false;

    protected bool $primary = false;

    protected bool $unique = false;

    protected bool $isUUID = false;

    public function __construct($attributes = [])
    {
        foreach ($attributes as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->$attribute = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function getMethodParameters(): array
    {
        return $this->methodParameters;
    }

    /**
     * @return string
     */
    public function getColumnDataType(): string
    {
        return $this->columnDataType;
    }

    /**
     * @return string|null
     */
    public function getColumnName(): ?string
    {
        return $this->columnName;
    }

    /**
     * @return bool
     */
    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    /**
     * @return ?bool
     */
    public function isNullable(): ?bool
    {
        return $this->nullable;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return string|null
     */
    public function getCollation(): ?string
    {
        return $this->collation;
    }

    /**
     * @return bool
     */
    public function isAutoIncrementing(): bool
    {
        return $this->autoIncrementing;
    }

    /**
     * @return bool
     */
    public function isPrimary(): bool
    {
        return $this->primary;
    }

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }

    /**
     * @return bool
     */
    public function isUUID(): bool
    {
        return $this->isUUID;
    }

    /**
     * @param string $columnDataType
     * @return ColumnDefinition
     */
    public function setColumnDataType(string $columnDataType): ColumnDefinition
    {
        $this->columnDataType = $columnDataType;

        return $this;
    }

    /**
     * @param array $methodParameters
     * @return ColumnDefinition
     */
    public function setMethodParameters(array $methodParameters): ColumnDefinition
    {
        $this->methodParameters = $methodParameters;

        return $this;
    }

    /**
     * @param string|null $columnName
     * @return ColumnDefinition
     */
    public function setColumnName(?string $columnName): ColumnDefinition
    {
        $this->columnName = $columnName;

        return $this;
    }

    /**
     * @param bool $unsigned
     * @return ColumnDefinition
     */
    public function setUnsigned(bool $unsigned): ColumnDefinition
    {
        $this->unsigned = $unsigned;

        return $this;
    }

    /**
     * @param ?bool $nullable
     * @return ColumnDefinition
     */
    public function setNullable(?bool $nullable): ColumnDefinition
    {
        $this->nullable = $nullable;

        return $this;
    }

    /**
     * @param mixed $defaultValue
     * @return ColumnDefinition
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @param string|null $comment
     * @return ColumnDefinition
     */
    public function setComment(?string $comment): ColumnDefinition
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @param string|null $collation
     * @return ColumnDefinition
     */
    public function setCollation(?string $collation): ColumnDefinition
    {
        $this->collation = $collation;

        return $this;
    }

    /**
     * @param bool $autoIncrementing
     * @return ColumnDefinition
     */
    public function setAutoIncrementing(bool $autoIncrementing): ColumnDefinition
    {
        $this->autoIncrementing = $autoIncrementing;

        return $this;
    }

    /**
     * @param bool $primary
     * @return ColumnDefinition
     */
    public function setPrimary(bool $primary): ColumnDefinition
    {
        $this->primary = $primary;

        return $this;
    }

    /**
     * @param bool $unique
     * @return ColumnDefinition
     */
    public function setUnique(bool $unique): ColumnDefinition
    {
        $this->unique = $unique;

        return $this;
    }

    /**
     * @param bool $isUUID
     * @return ColumnDefinition
     */
    public function setIsUUID(bool $isUUID): ColumnDefinition
    {
        $this->isUUID = $isUUID;

        return $this;
    }
}
