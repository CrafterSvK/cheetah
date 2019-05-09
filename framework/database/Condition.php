<?php
declare(strict_types=1);

namespace cheetah\database;

use mysqli;

/**
 * Condition (experimental feature)
 * @author Jakub Janek
 */
class Condition {
	private $parent;
	private $conditions = '';
	private $db;

	use ConditionTrait;

	/**
	 * Condition constructor.
	 * @param string operator
	 * @param $db mysqli connection object
	 * @param Query|Condition parent
	 */
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
			$value = !is_null($value) ? "'{$this->db->real_escape_string((string)$value)}'" : "NULL";
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

		if (isset($this->parent->join)) {
			$this->parent->join .= " ON {$this->conditions}";
			$this->parent->table .= " {$this->parent->join}";

			unset($this->parent->join);
		} else if (!is_null($this->parent)) {
			$this->parent->add($this->conditions);
		}

		return $this->parent ?? $this->conditions;
	}
}