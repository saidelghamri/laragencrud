<?php

namespace LaraCrud\Contracts;

interface ColumnContract
{
    /**
     * @return bool
     */
    public function isPk(): bool;

    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return string
     */
    public function label(): string;

    /**
     * @return TableContract
     */
    public function table(): TableContract;

    /**
     * @return bool
     */
    public function isFillable(): bool;

    /**
     * @return bool
     */
    public function isRequired(): bool;

    /**
     * @return bool
     */
    public function isNull(): bool;

    /**
     * @return bool
     */
    public function isUnique(): bool;

    /**
     * @return string
     */
    public function dataType(): string;

    /**
     * @return string
     */
    public function inputType(): string;

    /**
     * @return mixed
     */
    public function length();

    /**
     * @return int|null
     */
    public function order(): ?int;

    /**
     * @param int $number
     *
     * @return self
     */
    public function setOrder(int $number): self;

    /**
     * @return mixed
     */
    public function defaultValue();

    /**
     * @return string
     */
    public function image(): string;

    /**
     * @return string
     */
    public function file(): string;

    /**
     * @return array|null
     */
    public function options(): ?array;

    /**
     * @return \LaraCrud\Helpers\ForeignKey|null
     */
    public function foreignKey();

    /**
     * Request validation Rules.
     *
     * @return array
     */
    public function validationRules(): array;
}
