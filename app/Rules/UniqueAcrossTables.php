<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueAcrossTables implements ValidationRule
{
    protected $column;
    protected $tables;
    protected $excludedId;

    /**
     * Constructor to initialize the rule parameters.
     *
     * @param string $column
     * @param array $tables
     * @param int|null $excludedId
     */
    public function __construct(string $column, array $tables, ?int $excludedId = null)
    {
        $this->column = $column;
        $this->tables = $tables;
        $this->excludedId = $excludedId;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($this->tables as $table) {
            $query = DB::table($table)->where($this->column, $value);

            if ($this->excludedId) {
                $query->where('id', '<>', $this->excludedId);
            }

            if ($query->exists()) {
                $fail("The $attribute has already been taken in one of the records.");
                return;
            }
        }
    }
}