<?php
declare(strict_types=1);

namespace cheetah\database;

use mysqli;

/**
 * Condition (experimental feature)
 * @param string operator
 * @param mysqli database
 * @param Query|Condition parent
 */
class Condition {
	protected $operator;
	private $parent;
	private $conditions = '';
	private $db;

	public function __construct($operator, mysqli $db, $parent = null) {
		$this->operator = $operator;
		$this->parent = $parent;
		$this->db = $db;
	}

	/**
	 * Add condition
	 * @param array|string table.column | column
	 * @param array|string table.value | value
	 * @param string type of an operation
	 * @return Condition
	 */
	public function condition($column, $value, string $type = '='): Condition {
		if (is_array($column)) {
			$key = array_key_first($column);
			$column = "`{$key}`.{$column[$key]}";
		}

		if (is_array($value)) {
			$key = array_key_first($value);
			$value = "`{$key}`.{$value[$key]}";
		} else {
			$value = !is_null($value) ? $value = "'{$this->db->real_escape_string((string)$value)}'" : "NULL";
		}

		if (
			!empty($this->conditions)
			&& substr($this->conditions, 0, 1) !== "("
		) {
			$this->conditions = "({$this->conditions}";
		}

		$this->add("{$column} {$type} {$value}");

		return $this;
	}

	/**
	 * Creates itself for nested conditions
	 * @param string operator
	 * @return Condition
	 */
	public function conditions($operator): Condition {
		$condition = new self($operator, $this->db, $this);

		return $condition;
	}

	/**
	 * Closes brackets and adds itself to his parent
	 * @return Query|Condition|string
	 */
	public function close() {
		$this->conditions .= substr($this->conditions, 0, 1) === "(" ? ")" : "";

		if (!is_null($this->parent)) {
			$this->parent->add($this->conditions);
		}

		return $this->parent ?? $this->conditions;
	}

	/**
	 * Writes condition into string
	 * @param string condition
	 * @return void
	 */
	protected function add($conditionString): void {
		$this->conditions .= empty($this->conditions)
			? $conditionString
			: " {$this->operator} $conditionString";
	}

	/**
	 * Set operator of a next conditions to OR
	 * @return Condition
	 */
	public function or(): Condition {
		$this->operator = "OR";

		return $this;
	}

	/**
	 * Set operator of a next conditions to AND
	 * @return Condition
	 */
	public function and(): Condition {
		$this->operator = "AND";

		return $this;
	}

	/**
	 * Set operator of a next conditions to XOR
	 * @return Condition
	 */
	public function xor(): Condition {
		$this->operator = "XOR";

		return $this;
	}
}